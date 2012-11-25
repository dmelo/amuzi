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
        if (empty($artistMusicTitleIdSet)) {
            return array();
        } else {
            $db = $this->_musicSimilarityDb->getAdapter();
            $sqlIds = implode(', ', $artistMusicTitleIdSet);
            $sql = "(f_artist_music_title_id in ($sqlIds) AND ";
            $sql .= " s_artist_music_title_id in ($sqlIds)) ";
            if (false !== $degree)
                $sql .= $db->quoteInto(' AND degree = ?', $degree);

            return $this->_musicSimilarityDb->fetchAll($sql);
        }
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
        $rowSet = $this->findByArtistMusicTitleIdAndDegree(
            $artistMusicTitleId, $degree - 1
        );
        $ids = array();
        $similarities = array();
        $newRows = array();
        foreach ($rowSet as $row) {
            $ids[] = $row->fArtistMusicTitleId == $artistMusicTitleId ?
                $row->sArtistMusicTitleId : $row->fArtistMusicTitleId;
            $similarities[] = $row->similarity;
        }

        for ($i = 0; $i < count($ids); $i++) {
            for ($j = $i + 1; $j < count($ids); $j++) {
                if (($similarity =
                        ($similarities[$i] * $similarities[$j]) / 10000) >= 20
                ) {
                    $newRows[] = $this->packData(
                        $ids[$i], $ids[$j], $similarity, $degree
                    );
                }
            }
        }

        $ret = $this->_musicSimilarityDb->insertMulti($newRows);

        return array(
            'tried to insert' => count($newRows),
            'requests' => $ret[0],
            'inserted' => $ret[1]
        );
    }

    /**
     * getSimilarityMatrix Calculate the similarity matrix for a given set of
     * elements
     *
     * @param mixed $list List of artist_music_title_id.
     * @return Returns an array with three elements: the similarity matrix
     * itself, the total number of elements and the quality of the similarity
     * matrix which is measured by N / M, where N is the number of non-zero
     * elements and M is the total number of elements on the matrix.
     */
    public function getSimilarityMatrix($list)
    {
        $this->_logger->debug('MS::getSimilarityMatrix A ' . microtime(true));
        $rowSet = $this->findByArtistMusicTitleIdSetAndDegree($list, false);
        $this->_logger->debug('MS::getSimilarityMatrix B ' . microtime(true));
        $matrix = array();
        foreach ($list as $a) {
            $matrix[$a] = array();
            foreach ($list as $b)
                $matrix[$a][$b] = 0;
        }
        $this->_logger->debug(
            'MS::getSimilarityMatrix C ' . microtime(true) .
            ' -- ' . count($rowSet)
        );
        $count = 0;

        foreach ($rowSet as $row) {
            $count++;
            $a = $row->fArtistMusicTitleId;
            $b = $row->sArtistMusicTitleId;
            $matrix[$a][$b] = $matrix[$b][$a] = (int) $row->similarity;
        }
        $this->_logger->debug(
            'MusicSimilarity::getSimilarityMatrix D ' .
            microtime(true) . "#" . $count
        );

        $total = count($list);
        $quality = $count / (($total * $total * 0.5) + 1);


        return array($matrix, $total, $quality);
    }

    /**
     * Returns an array with two elements. The first is an array of similar
     * elements, each element containing artist and musicTitle. The second
     * element is the similarity matrix.
     */
    public function getSimilar(
        $artist, $musicTitle, $artistMusicTitleIdList = array()
    )
    {
        $artistMusicTitleId = $this->_artistMusicTitleModel->insert(
            $artist, $musicTitle
        );
        $similarList = $this->_musicSimilarityDb->getSimilar(
            $artist, $musicTitle, $artistMusicTitleIdList
        );
        $idsList = array($artistMusicTitleId);
        foreach ($similarList as $entry) {
            $idsList[] = $entry['artist_music_title_id'];
        }


        if (empty($idsList)) {
            $this->_logger->debug("MusicSimilarity::getSimilar local empty");
            return $this->searchSimilarSync(
                $artist, $musicTitle, $artistMusicTitleIdList
            );
        }

        $completeIdsList = array();
        foreach ($artistMusicTitleIdList as $artistMusicTitleId) {
            $completeIdsList[] = $artistMusicTitleId;
        }
        $completeIdsList = array_merge($completeIdsList, $idsList);

        $similarityMatrixResponse = $this->getSimilarityMatrix(
            $completeIdsList
        );

        $this->_logger->debug(
            "MusicSimilarity::getSimilar local quality{ size: "
            . $similarityMatrixResponse[1] . ". non-zero: "
            . $similarityMatrixResponse[2]
        );

        if (
            $similarityMatrixResponse[1] < 20 ||
            $similarityMatrixResponse[2] < 0.03
        ) {
            return $this->searchSimilarSync(
                $artist, $musicTitle, $artistMusicTitleIdList
            );
        }

        $artistAndMusicTitleList = $this->_artistMusicTitleModel
            ->fetchAllArtistAndMusicTitle($idsList);

        // If local information is used, then there must be a task to
        // refresh current data.
        $this->_taskRequestModel->addTask(
            'SearchSimilar', $artist, $musicTitle
        );

        return array(
            $artistAndMusicTitleList,
            $similarityMatrixResponse[0]
        );
    }

    public function searchSimilarSync(
        $artist, $musicTitle, $artistMusicTitleIdList = array()
    )
    {
        $rowSet = $this->_lastfmModel->getSimilar($artist, $musicTitle);
        $artistMusicTitleId = $this->_artistMusicTitleModel->insert(
            $artist, $musicTitle
        );
        $artistMusicTitleIdList[] = $artistMusicTitleId;
        $list = array(array(
            'artist' => $artist,
            'musicTitle' => $musicTitle,
            'artistMusicTitleId' => $artistMusicTitleId
        ));

        foreach ($rowSet as $row) {
            $sArtistMusicTitleId = $this->_artistMusicTitleModel->insert(
                $row->artist, $row->musicTitle
            );

            if (null !== $sArtistMusicTitleId) {
                $this->_musicSimilarityModel->insert(
                    $artistMusicTitleId,
                    $sArtistMusicTitleId,
                    $row->similarity
                );
            }

            if (
                array_search(
                    $sArtistMusicTitleId, $artistMusicTitleIdList
                ) === false
            ) {
                $list[] = array(
                    'artist' => $row->artist,
                    'musicTitle' => $row->musicTitle,
                    'artistMusicTitleId' => $sArtistMusicTitleId
                );

                $artistMusicTitleIdList[] = $sArtistMusicTitleId;
            }
        }

        $this->_musicSimilarityModel->calcSimilarityDegree(
            $artistMusicTitleId
        );

        $similarityMatrixResponse = $this->_musicSimilarityModel
        ->getSimilarityMatrix(
            $artistMusicTitleIdList
        );

        return array(
            $list,
            $similarityMatrixResponse[0]
        );
    }
}
