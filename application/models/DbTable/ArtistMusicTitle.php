<?php

/**
 * DbTable_ArtistMusicTitle
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
class DbTable_ArtistMusicTitle extends DZend_Db_Table
{
    protected $_allowRequestCache = true;

    public function insert($data)
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

    /**
     * autocomplete Search on database for rows that fits the query.
     *
     * @DEPRECATED: as tables have millions of rows, a like on a join with
     * three tables will take too long.
     *
     * @param mixed $q
     * @param int $limit
     * @return Zend_Db_Table_RowSet
     */
    /*
    public function autocomplete($q, $limit = 10)
    {
        $db = $this->getAdapter();
        $select = $db->select();
        $select->from(
            array('a' => 'artist'),
            array(
                'name' => 'concat(a.name, " - ", mt.name)',
                'cover' => 't.cover',
                'artist' => 'name',
                'musicTitle' => 'mt.name'
                )
        )->join(
            array('amt' => 'artist_music_title'),
            'amt.artist_id = a.id',
            array()
        )->join(
            array('mt' => 'music_title'),
            'mt.id = amt.music_title_id',
            array()
        )->join(
            array('mtl' => 'music_track_link'),
            'mtl.artist_music_title_id = amt.id',
            array()
        )->join(array('t' => 'track'), 't.id = mtl.track_id', array())
        ->where('concat(a.name, " - ", mt.name) like ?', '%' . $q . '%')
        ->group('amt.id')
        ->limit($limit);

        $this->_logger->debug("-----> select: " . $select);
        return $db->fetchAll($select);
    }
    */

    public function autocomplete($data)
    {
        $this->_logger->debug("ArtistMusicTitle::autocomplete " . print_r($data, true));
        $ret = array();

        $db = $this->getAdapter();
        $select = $db->select();
        $select->from(
            array('a' => 'artist'),
            array('amtid' => 'amt.id',
                'artistId' => 'amt.artist_id',
                'musicTitleId' => 'amt.music_title_id')
        )->join(
            array('amt' => 'artist_music_title'),
            'amt.artist_id = a.id',
            array()
        )->join(
            array('mt' => 'music_title'),
            'mt.id = amt.music_title_id',
            array())
        ->limit(5);

        if (array_key_exists('artist', $data)) {
            $where[] = $db->quoteInto('a.name like ?', '%' . $data['artist'] . '%');
        }

        if (array_key_exists('music_title', $data)) {
            $where[] = $db->quoteInto('mt.name like ?', '%' . $data['music_title'] . '%');
        }

        $where = implode(' AND ', $where);
        $select->where($where);

        $rowSet = $db->fetchAll($select);
        $artistIdSet = array();
        $artistMusicTitleIdSet = array();
        foreach ($rowSet as $row) {
            $artistIdSet[] = $row['artistId'];
            $artistMusicTitleIdSet[] = $row['amtid'];
        }
        $artistModel = new Artist();
        $artistMusicTitleModel = new ArtistMusicTitle();
        $artistModel->preload($artistIdSet);

        $ret = array();
        foreach ($artistMusicTitleModel->findById($artistMusicTitleIdSet) as $artistMusicTitleRow) {
            $ret[] = new AutocompleteEntry(
                $artistMusicTitleRow->getArtistName(),
                $artistMusicTitleRow->getMusicTitleName(),
                $artistMusicTitleRow->getCover(),
                'track'
            );
        }

        return $ret;
    }
}
