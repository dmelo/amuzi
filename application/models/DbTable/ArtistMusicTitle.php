<?php

/**
 * DbTable_ArtistMusicTitle
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
class DbTable_ArtistMusicTitle extends DZend_Db_Table
{
    protected $_allowRequestCache = true;

    public function insert(array $data)
    {
        return $this->insertCachedWithoutException($data);
    }

    public function fetchAllArtistAndMusicTitle($idList)
    {
        $db = $this->getAdapter();
        $select = $db->select();
        $select->from(
            array('amt' => 'artist_music_title'),
            array('id' => 'id')
        )->join(
            array('a' => 'artist'),
            'a.id = amt.artist_id',
            array('artist' => 'name')
        )->join(
            array('m' => 'music_title'),
            'm.id = amt.music_title_id',
            array('musicTitle' => 'name')
        )->where('amt.id in ( ' . implode(', ', $idList) . ')');

        return $db->fetchAll($select);
    }

    public function fetchAllByArtistAndMusicTitle($rowSet)
    {
        $db = $this->getAdapter();
        $where = '';
        $first = true;
        foreach ($rowSet as $row) {
            if ($first) {
                $first = false;
            } else {
                $where .= ' OR ';
            }
            $where .= $db->quoteInto(' ( a.name = ? AND ', $row->artist)
                . $db->quoteInto(' m.name = ? ) ', $row->musicTitle);
        }

        $select = $db->select()->from(
            array('amt' => 'artist_music_title'),
            array('id' => 'id')
        )->join(
            array('a' => 'artist'),
            'a.id = amt.artist_id',
            array('artist' => 'name')
        )->join(
            array('m' => 'music_title'),
            'm.id = amt.music_title_id',
            array('musicTitle' => 'name')
        )->where($where);

        $this->_logger->debug('ArtistMusicTitle::insertMulti query ' . $select);

        return $db->fetchAll($select);
    }
}
