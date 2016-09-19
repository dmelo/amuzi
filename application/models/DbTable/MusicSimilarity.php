<?php

/**
 * DbTable_MusicSimilarity
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
class DbTable_MusicSimilarity extends DZend_Db_Table
{
    protected $_allowRequestCache = true;

    public function insert(array $data)
    {
        Zend_Registry::get('logger')->debug(
            "DbTable_MusicSimilarity::insert " . print_r($data, true)
        );
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
        $c = new DZend_Chronometer();
        $c->start();
        $db = $this->getAdapter();

        $where = '';
        if (is_array($artistMusicTitleId)) {
            if (0 != count($artistMusicTitleId)) {
                $ids = implode(', ', $artistMusicTitleId);
                $where = "( f_artist_music_title_id in ($ids) or " .
                    "s_artist_music_title_id in ($ids) )";
            } else {
                $where = ' 1 = 0 ';
            }
        } else {
            $where = "( f_artist_music_title_id = $artistMusicTitleId " .
                "or s_artist_music_title_id = $artistMusicTitleId ) ";
        }

        $exclusionList = array();

        foreach ($artistMusicTitleIdList as $amtId) {
            if ($amtId != $artistMusicTitleId) {
                $exclusionList[] = $amtId;
            }
        }

        if (count($exclusionList) > 0) {
            $where .= ' AND f_artist_music_title_id not in ( '
                . implode(', ', $exclusionList). ' ) AND '
                . ' s_artist_music_title_id not in ( '
                . implode(', ', $exclusionList) . ' )';
        }

        Zend_Registry::get('logger')->debug(
            "DbTable_MusicSimilarity::getSimilar $where"
        );

        $ret = array();
        $rowSet = $this->fetchAll($where, 'similarity desc', 50);
        foreach ($rowSet as $row) {
            $ret[] = $artistMusicTitleId == $row->fArtistMusicTitleId ?
                $row->sArtistMusicTitleId : $row->fArtistMusicTitleId;
        }
        $c->stop();
        $this->_logger->debug(
            "DbTable_MusicSimilarity::getSimilar time: " . $c->get()
        );

        return $ret;
    }

    public function insertMultipleRows($artistMusicTitleId, array $rowSet)
    {
        $where = '';
        $first = true;
        foreach ($rowSet as $row) {
            if ($first) {
                $first = false;
            } else {
                $where .= ' OR ';
            }

            $where .= ' (f_artist_music_title_id = ' . $artistMusicTitleId
                . ' AND s_artist_music_title_id = ' . $row->id . ') OR ('
                . 'f_artist_music_title_id = ' . $row->id . ' AND '
                . 's_artist_music_title_id = ' . $artistMusicTitleId . ') ';
        }

        $msRowSet = $this->fetchAll($where);
        $similarities = array();
        foreach ($msRowSet as $row) {
            $f = $row->fArtistMusicTitleId;
            $s = $row->sArtistMusicTitleId;
            if (!array_key_exists($f, $similarities)) {
                $similarities[$f] = array();
            }

            if (!array_key_exists($s, $similarities)) {
                $similarities[$s] = array();
            }

            $similarities[$f][$s] = $row->similarity;
            $similarities[$s][$f] = $row->similarity;
        }

        foreach ($rowSet as $row) {
            if (!array_key_exists($row->id, $similarities)
                || !array_key_exists(
                    $artistMusicTitleId, $similarities[$row->id]
                ) || $row->similarity
                    != $similarities[$row->id][$artistMusicTitleId]
                ) {
                $this->insert(
                    array(
                        'f_artist_music_title_id' => $artistMusicTitleId,
                        's_artist_music_title_id' => $row->id,
                        'similarity' => $row->similarity
                    )
                );
            }
        }
    }
}
