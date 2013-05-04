<?php

/**
 * DbTable_Album
 *
 * @package Amuzi
 * @version 1.0
 * Amuzi - Online music
 * Copyright (C) 2010-2013  Diogo Oliveira de Melo
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
    public function insert($data)
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

    public function autocomplete($data)
    {
        $db = $this->getAdapter();
        $select = $db->select();
        $select->from(
            array('a' => 'artist'),
            array(
                'name' => 'album.name',
                'cover' => 'album.cover',
                'artist_name' => 'a.name'
            )
        )->join(
            array('album' => 'album'),
            'album.artist_id = a.id'
        )->limit(5);

        $where = array();
        if (array_key_exists('artist', $data)) {
            $where[] = $db->quoteInto(
                'a.name like ?', '%' . $data['artist'] . '%'
            );
        }

        if (array_key_exists('album', $data)) {
            $where[] = $db->quoteInto(
                'album.name like ?', '%' . $data['album'] . '%'
            );
        }

        $select->where(implode(' AND ', $where));
        $this->_logger->debug("DbTable_Album::autocomplete " . $select);
        $rowSet = $db->fetchAll($select);
        $ret = array();
        foreach ($rowSet as $row) {
            $ret[] = new AutocompleteEntry(
                $row['artist_name'],
                $row['name'],
                $row['cover'],
                'album'
            );
        }

        return $ret;
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
}
