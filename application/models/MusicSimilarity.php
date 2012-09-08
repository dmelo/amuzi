<?php

/**
 * MusicSimilarity Controllers interact with music_similarity db table through
 * this class.
 *
 * @package amuzi
 * @version 1.0
 * Amuzi - Online music
 * Copyright (C) 2010-2012  Diogo Oliveira de Melo
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
class MusicSimilarity extends DZend_Model
{
    public function packData(
        $fArtistMusicTitleId, $sArtistMusicTitleId, $similarity, $degree = 0
    )
    {
        list($f, $s) = $fArtistMusicTitleId < $sArtistMusicTitleId ?
            array($fArtistMusicTitleId, $sArtistMusicTitleId):
            array($sArtistMusicTitleId, $fArtistMusicTitleId);

        return array('f_artist_music_title_id' => $f,
            's_artist_music_title_id' => $s,
            'similarity' => $similarity,
            'degree' => $degree
        );
    }

    /**
     * insert Inset an element.
     *
     * @param mixed $fArtistMusicTitleId First artist_music_title id.
     * @param mixed $sArtistMusicTitleId Second artist_music_title id.
     * @param mixed $similarity How similar this two rows are, from 1 to 10000.
     * @return void Returns the id of the row inserted in case of success, null 
     * otherwise.
     */
    public function insert(
        $fArtistMusicTitleId, $sArtistMusicTitleId, $similarity, $degree = 0
    )
    {
        return $this->_musicSimilarityDb->insert(
            $this->packData(
                $fArtistMusicTitleId,
                $sArtistMusicTitleId,
                $similarity,
                $degree
            )
        );
    }

    public function findByArtistMusicTitleIdAndDegree(
        $artistMusicTitleId, $degree = 0
    )
    {
        $db = $this->_musicSimilarityDb->getAdapter();
        $sql = $db->quoteInto(
            '(f_artist_music_title_id = ?', $artistMusicTitleId
        ) .
        $db->quoteInto(' OR s_artist_music_title_id = ?)', $artistMusicTitleId);
        if (false !== $degree)
            $sql .= $db->quoteInto(' AND degree = ?', $degree);

        return $this->_musicSimilarityDb->fetchAll($sql, 'similarity desc');
    }

    public function findByArtistMusicTitleIdSetAndDegree(
        $artistMusicTitleIdSet, $degree = 0
    )
    {
        $db = $this->_musicSimilarityDb->getAdapter();
        $sqlIds = implode(', ', $artistMusicTitleIdSet);
        $sql = "(f_artist_music_title_id in ($sqlIds) OR ";
        $sql .= " s_artist_music_title_id in ($sqlIds)) ";
        if (false !== $degree)
            $sql .= $db->quoteInto(' AND degree = ?', $degree);

        return $this->_musicSimilarityDb->fetchAll($sql);
    }

    public function getRandomArtistMusicTitleId()
    {
        return $this->_musicSimilarityDb->getRandomArtistMusicTitleId();
    }

    public function test()
    {
        $this->_musicSimilarityDb->test();
    }

    public function calcSimilarityDegree($artistMusicTitleId, $degree = 1)
    {
        $rowSet = $this->findByArtistMusicTitleIdAndDegree($artistMusicTitleId, $degree - 1);
        $ids = array();
        $similarities = array();
        $newRows = array();
        foreach ($rowSet as $row) {
            $ids[] = $row->fArtistMusicTitleId == $artistMusicTitleId ? $row->sArtistMusicTitleId : $row->fArtistMusicTitleId;
            $similarities[] = $row->similarity;
        }

        for ($i = 0; $i < count($ids); $i++)
            for ($j = $i + 1; $j < count($ids); $j++)
                if (($similarity = ($similarities[$i] * $similarities[$j]) / 10000) > -1) // Threashold.
                    $newRows[] = $this->packData($ids[$i], $ids[$j], $similarity, $degree);

        $ret = $this->_musicSimilarityDb->insertTree($newRows);

        return array(
            'tried to insert' => count($newRows),
            'requests' => $ret[0],
            'inserted' => $ret[1]
        );
    }

    public function getSimilarityMatrix($list)
    {
        $rowSet = $this->findByArtistMusicTitleIdSetAndDegree($list, false);
        $matrix = array();
        foreach ($list as $a) {
            $matrix[$a] = array();
            foreach ($list as $b)
                $matrix[$a][$b] = 0;
        }

        foreach ($rowSet as $row) {
            $a = $row->fArtistMusicTitleId;
            $b = $row->sArtistMusicTitleId;
            if (in_array($a, $list) && in_array($b, $list))
                $matrix[$a][$b] = $matrix[$b][$a] = (int) $row->similarity;
        }

        return $matrix;
    }
}
