<?php

/**
 * DbTable_MusicSimilarity
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
class DbTable_MusicSimilarity extends DZend_Db_Table
{
    public function insert($data)
    {
        try {
            return $this->insertCachedWithoutException($data);
        } catch(Zend_Db_Statement_Exception $e) {
            $f = $data['f_artist_music_title_id'];
            $s = $data['s_artist_music_title_id'];

            $db = $this->getAdapter();
            $this->update(
                array('similarity' => $data['similarity']),
                $db->quoteInto('f_artist_music_title_id = ?', $f) .
                $db->quoteInto(' AND s_artist_music_title_id = ?', $s)
            );

            $row = $this->findRowByFArtistMusicTitleIdAndSArtistMusicTitleId(
                $f, $s
            );
            // TODO: Uncomment when cache mechanism is fixed.
            // $this->_cache->save($row->id, $this->getCacheKey($data));
            return $row->id;
        }
    }

    public function getRandomArtistMusicTitleId()
    {
        $column = rand() % 2 ?
            'f_artist_music_title_id' : 's_artist_music_title_id';
        $select = $this->select()
            ->where('1 = 1')
            ->order('rand()')
            ->group($column);
        $row = $this->fetchRow($select);
        return $row->$column;
    }

    public function getSimilar($artistMusicTitleId, $artistMusicTitleIdList)
    {
        $db = $this->getAdapter();

        $where = "( f_artist_music_title_id = $artistMusicTitleId " .
            "or s_artist_music_title_id = $artistMusicTitleId ) ";
        foreach ($artistMusicTitleIdList as $amtId) {
            if ($amtId != $artistMusicTitleId) {
            $where .= " AND f_artist_music_title_id != $amtId "
                . "AND s_artist_music_title_id != $amtId ";
            }
        }

        $ret = array();
        $rowSet = $this->fetchAll($where, 'similarity desc', 100);
        foreach ($rowSet as $row) {
            $ret[] = $artistMusicTitleId == $row->fArtistMusicTitleId ?
                $row->sArtistMusicTitleId : $row->fArtistMusicTitleId;
        }

        return $ret;
    }
}
