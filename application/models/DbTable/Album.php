<?php

/**
 * DbTable_Album
 *
 * @package Amuzi
 * @version 1.0
 * Amuzi - Online music
 * Copyright (C) 2010-2014  Diogo Oliveira de Melo
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
class DbTable_Album extends DZend_Db_Table
{
    public function insert(array $data)
    {
        // TODO: trim name and cover.
        if (array_key_exists('name', $data)) {
            $data['name'] = substr($data['name'], 0, 254);
        }

        $cover = null;
        if (array_key_exists('cover', $data) && null !== $data['cover']) {
            $data['cover'] = substr($data['cover'], 0, 2046);
            $cover = $data['cover'];
            unset($data['cover']);
        }

        $id = $this->insertCachedWithoutException($data);

        if (null !== $cover) {
            $albumRow = $this->findRowById($id);
            $albumRow->cover = $cover;
            $albumRow->save();
        }

        return $id;
    }

    public function fetchAllArtistAndAlbum($idList)
    {
        $db = $this->getAdapter();
        $select = $db->select();
        $select->from(
            array('album' => 'album'),
            array('id' => 'id', 'name', 'name')
        )->join(
            array('a' => 'artist'),
            'a.id = album.artist_id',
            array('artist' => 'name')
        )->where('album.id in ( ' . implode(', ', $idList) . ')');

        return $db->fetchAll($select);
    }

    public function fetchNextAlbumRow($id)
    {
        return $this->fetchRow(
            $this->select()
                ->where($this->_db->quoteInto('id > ?', $id))
                ->order('id')
        );
    }
}
