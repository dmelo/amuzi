<?php

/**
 * MusicSimilarity Controllers interact with music_similarity db table through
 * this class.
 *
 * @package amuzi
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
class MusicSimilarity extends DZend_Model
{
    public function packData(
        $fArtistMusicTitleId, $sArtistMusicTitleId, $similarity, $degree = 0
    )
    {
        return array(
            'f_artist_music_title_id' => $fArtistMusicTitleId,
            's_artist_music_title_id' => $sArtistMusicTitleId,
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
            return $this->_musicSimilarityDb
                ->findByFArtistMusicTitleIdAndSArtistMusicTitleIdAndDegree(
                    $artistMusicTitleIdSet, $artistMusicTitleIdSet, $degree
                );
        }
    }

    public function getRandomArtistMusicTitleId()
    {
        return $this->_musicSimilarityDb->getRandomArtistMusicTitleId();
    }

    private function _similarityToDissimilarity($m)
    {
        foreach ($m as $i => $row) {
            foreach ($row as $j => $val) {
                $m[$i][$j] = 1.0 - $m[$i][$j];
            }
        }

        return $m;
    }

    private function _matrixToString($m)
    {
        $s = '';
        foreach ($m as $i => $row) {
            foreach ($row as $j => $cell) {
                $s .= sprintf(' %05d', $cell);
            }
            $s .= PHP_EOL;
        }

        return $s;
    }

    private function _enhanceMatrix($m)
    {
        $c = new DZend_Chronometer();
        $c->start();
        $mIsSquared = true;
        $rows = 0;
        $colsSet = array();
        // Normalize
        foreach ($m as $i => $row) {
            $totalRow = 0;
            $rows++;
            foreach ($row as $j => $cell) {
                if (!array_key_exists($i, $colsSet)) {
                    $colsSet[$i] = 0;
                }
                $colsSet[$i]++;
                $totalRow += $cell;
            }

            if ($totalRow > 0) {
                foreach ($row as $j => $cell) {
                    $m[$i][$j] = $cell / $totalRow;
                }
            }
        }

        foreach ($colsSet as $cols) {
            if ($cols !== $rows) {
                $mIsSquared = false;
                break;
            }
        }

        if (!$mIsSquared) {
            $ret = null;
        } else {
            // Multiply by itself
            $mSquared = array();
            foreach ($m as $i => $row) {
                foreach ($row as $j => $cell) {
                    $mSquared[$i][$j] = 0;
                    foreach ($row as $k => $aux) {
                        $mSquared[$i][$j] += $m[$i][$k] * $m[$k][$j];
                    }
                }
            }

            // Aggregate the two matrices
            foreach ($m as $i => $row) {
                foreach ($row as $j => $cell) {
                    if (0 == $cell) {
                        $m[$i][$j] = $mSquared[$i][$j];
                    }
                }
            }

            $ret = $m;
        }


        $c->stop();
        $this->_logger->debug(
            'MusicSimilarity::_enhanceMatrix time: ' . $c->get()
        );

        return $ret;
    }

    private function _logSparsity($m)
    {
        $c = new DZend_Chronometer();
        $mSquared = array();
        $c->start();
        $total = 0;
        $zeros = 0;
        $zerosSquared = 0;


        $cols = 0;
        $rows = 0;

        // Uncomment for the detailed log
        /*
        $this->_logger->debug(
            'MusicSimilarity::_logSparsity M ' . PHP_EOL
            . $this->_matrixToString($m)
        );
        */

        foreach ($m as $i => $row) {
            $rows++;
            foreach ($row as $j => $val) {
                $total++;
                $zeros += 0 === $m[$i][$j] ? 1 : 0;
                $cols++;
            }
        }

        if ($rows > 0) {
            $cols /= $rows;
        }

        if ($cols == $rows) {
            foreach ($m as $i => $row) {
                foreach ($row as $j => $cell) {
                    if (!array_key_exists($i, $mSquared)) {
                        $mSquared[$i] = array();
                    }
                    if (!array_key_exists($j, $mSquared[$i])) {
                        $mSquared[$i][$j] = (int) 0;
                    }

                    foreach ($row as $k => $cell) {
                        $mSquared[$i][$j] += (int) ($m[$i][$k] * $m[$k][$j]);
                        if ($mSquared[$i][$j] > 0) {
                            break;
                        }
                    }
                }
            }

            foreach ($mSquared as $i => $row) {
                foreach ($row as $j => $cell) {
                    if (0 === $cell) {
                        $zerosSquared++;
                    }
                }
            }
        } else {
            $this->_logger->err(
                "There is a similarity matrix that have $rows rows and "
                . "$cols cols."
            );
        }

        $this->_logModel->insertSparsity($zeros, $zerosSquared, $total);
        $c->stop();
        $this->_logger->debug(
            'MusicSimilarity::_logSparsity time: ' . $c->get()
        );
    }

    /**
     * _getSimilarityMatrix Calculate the similarity matrix for a given set of
     * elements
     *
     * @param mixed $list List of artist_music_title_id.
     * @return Returns an array with three elements: the similarity matrix
     * itself, the total number of elements and the quality of the similarity
     * matrix which is measured by N / M, where N is the number of non-zero
     * elements and M is the total number of elements on the matrix.
     */
    protected function _getSimilarityMatrix($list)
    {
        $this->_logger->debug('MS::_getSimilarityMatrix A ' . microtime(true));
        $rowSet = $this->findByArtistMusicTitleIdSetAndDegree($list, false);
        $this->_logger->debug('MS::_getSimilarityMatrix B ' . microtime(true));
        $matrix = array();
        foreach ($list as $a) {
            $matrix[$a] = array();
            foreach ($list as $b) {
                $matrix[$a][$b] = 0;
            }
        }
        $this->_logger->debug(
            'MS::_getSimilarityMatrix C ' . microtime(true) .
            ' -- ' . count($rowSet)
        );
        $count = 0;

        foreach ($rowSet as $row) {
            $count++;
            $a = $row->fArtistMusicTitleId;
            $b = $row->sArtistMusicTitleId;
            $matrix[$a][$b] = (int) $row->similarity;
        }

        // TODO: Extrapolate matrix using markov.

        // from last.fm semantic to incBoard semantic.
        foreach ($list as $a) {
            foreach ($list as $b) {
                if (0 !== $matrix[$a][$b] && 0 !== $matrix[$b][$a]) {
                    $matrix[$a][$b] = $matrix[$b][$a] = (int) ($matrix[$a][$b]
                        + $matrix[$b][$a]) / 2;
                } elseif (0 !== $matrix[$a][$b]) {
                    $matrix[$b][$a] = $matrix[$a][$b];
                } elseif (0 !== $matrix[$b][$a]) {
                    $matrix[$a][$b] = $matrix[$b][$a];
                }
            }
        }

        $this->_logger->debug(
            'MS::_getSimilarityMatrix D ' .
            microtime(true) . "#" . $count
        );

        $total = count($list);
        $quality = $count / (($total * $total * 0.5) + 1);


        return array($matrix, $total, $quality);
    }

    protected function _applyListTranslationToMatrix(
        $similarityMatrix, $translationList
    )
    {
        $rows = 0;
        $cols = 0;

        foreach ($similarityMatrix as $row) {
            $rows++;
            foreach ($row as $cell) {
                $cols++;
            }
        }

        if ($rows > 0) {
            $cols /= $rows;
        }
        $this->_logger->debug(
            "MusicSimilarity::_applyListTranslationToMatrix A0 rows"
            . " $rows. cols $cols"
        );

        $amtList = array();
        foreach ($similarityMatrix as $id => $cols) {
            $amtList[] = $id;
        }

        foreach ($translationList as $albumId => $amtIdSet) {
            $total = count($amtIdSet);
            // Fill the column
            foreach ($amtList as $i) {
                $sum = 0;
                foreach ($amtIdSet as $j) {
                    if (array_key_exists($i, $similarityMatrix) &&
                        array_key_exists($j, $similarityMatrix[$i])) {
                        $sum += $similarityMatrix[$i][$j];
                    }
                }
                $similarityMatrix[$i][-$albumId] = $total > 0 ?
                    (int) $sum / count($amtIdSet) : 0;
            }

            // Fill the row
            $similarityMatrix[-$albumId] = array();
            foreach ($amtList as $j) {
                $similarityMatrix[-$albumId][$j] = 0;
                $sum = 0;
                foreach ($amtIdSet as $i) {
                    if (array_key_exists($i, $similarityMatrix) &&
                        array_key_exists($j, $similarityMatrix[$i])) {
                        $sum += $similarityMatrix[$i][$j];
                    }
                }
                $similarityMatrix[-$albumId][$j] = $total > 0 ?
                    (int) $sum / count($amtIdSet) : 0;
            }

            $similarityMatrix[-$albumId][-$albumId] = 0;

            $amtList[] = -$albumId;
        }

        foreach ($translationList as $albumId => $amtIdSet) {
            // erase replaced rows.
            foreach ($amtIdSet as $i) {
                unset($similarityMatrix[$i]);
            }

            // erase replace columns.
            foreach ($amtList as $i) {
                foreach ($amtIdSet as $j) {
                    unset($similarityMatrix[$i][$j]);
                }
            }
        }

        $rows = 0;
        $cols = 0;

        foreach ($similarityMatrix as $row) {
            $rows++;
            foreach ($row as $cell) {
                $cols++;
            }
        }

        if ($rows > 0) {
            $cols /= $rows;
        }
        $this->_logger->debug(
            "MusicSimilarity::_applyListTranslationToMatrix A0 rows"
            . " $rows. cols $cols"
        );


        return $similarityMatrix;
    }

    protected function _applyListTranslationToList($objList, $translationList)
    {
        foreach ($translationList as $albumId => $amtIdList) {
            $first = true;
            foreach ($amtIdList as $amtId) {
                if (($key = array_search($amtId, $objList)) !== false) {
                    // The album Id will take the place of one of the AMTIds
                    // it is replacing.
                    if ($first) {
                        $objList[$key] = -$albumId;
                        $first = false;
                    } else {
                        unset($objList[$key]);
                    }
                }
            }
        }

        return $objList;
    }

    protected function _fetchObjList($idList)
    {
        $amtIdList = array();
        $indexAmtIdList = array();
        $albumIdList = array();
        $indexAlbumIdList = array();
        $ret = array();

        foreach ($idList as $id) {
            if ($id < 0) {
                $albumIdList[] = -$id;
            } else {
                $amtIdList[] = $id;
            }
        }


        $amtList = empty($amtIdList) ?
            array() : $this->_artistMusicTitleModel
                ->fetchAllArtistAndMusicTitle($amtIdList);
        foreach ($amtList as &$row) {
            $row['type'] = 'track';
            $row['objId'] = $row['id'];
            $indexAmtIdList[$row['objId']] = $row;
        }

        $albumList = empty($albumIdList) ?
            array() : $this->_albumModel->fetchAllArtistAndAlbum($albumIdList);
        foreach ($albumList as &$row) {
            $row['type'] = 'album';
            $row['objId'] = -$row['id'];
            $indexAlbumIdList[$row['objId']] = $row;
        }

        foreach ($idList as $id) {
            if ($id < 0) {
                $ret[] = $indexAlbumIdList[$id];
            } else {
                $ret[] = $indexAmtIdList[$id];
            }
        }

        return $ret;
    }

    public function _replaceAlbumIdByAMTIds($objIds)
    {
        $ret = array();
        $albumIdList = array();
        $translationList = array();
        foreach ($objIds as $id) {
            if ($id > 0) {
                $ret[] = $id;
            } else {
                $albumIdList[] = -$id;
            }
        }

        foreach ($albumIdList as $albumId) {
            $albumRow = $this->_albumModel->findRowById($albumId);
            $translationList[$albumId] = $albumRow->artistMusicTitleIdList;
            $ret = array_merge($ret, $translationList[$albumId]);
        }

        return array($ret, $translationList);
    }

    /**
     * Returns an array with two elements. The first is an array of similar
     * elements, each element containing artist and musicTitle. The second
     * element is the similarity matrix.
     */
    public function getSimilar(
        $artist, $musicTitle, $type, $extObjIdList = array(), $mayUseSync = true
    )
    {
        $this->_logger->debug(
            "MusicSimilarity::getSimilar($artist, $musicTitle, $type);"
        );
        $this->_logger->debug(
            "MusicSimilarity::getSimilar -- extObjIdList: "
        );

        $c = new DZend_Chronometer();
        $c->start();
        // Get the center object.
        if ('album' === $type) {
            $albumRow = $this->_albumModel->get($artist, $musicTitle);
            $extObjIdList[] = -$albumRow->id;
            $artistMusicTitleId = array();
            foreach ($albumRow->artistMusicTitleIdList as $id) {
                $artistMusicTitleId[] = $id;
            }
        } else {
            $artistMusicTitleId = $this->_artistMusicTitleModel->insert(
                $artist, $musicTitle
            );
        }

        // Get the AMTIds that are not yet on $extObjIdList neither is on
        // AMTIds owned by the albums.
        list($allAMTId, $blank) = $this->_replaceAlbumIdByAMTIds($extObjIdList);
        $similarList = $this->_musicSimilarityDb->getSimilar(
            $artistMusicTitleId, $allAMTId
        );

        $this->_logger->debug(
            "MusicSimilarity::getSimilar $artist,$musicTitle,$type,$mayUseSync"
        );

        $ret = null;
        // If nothing is found, use sync.
        if (empty($similarList)) {
            if ($mayUseSync) {
                if ('album' === $type && count($artistMusicTitleId) > 0) {
                    $artistMusicTitleId = min($artistMusicTitleId);
                    $type = 'track';
                } elseif ('album' === $type
                    && count($artistMusicTitleId) === 0) {
                    $ret = array(array(), $extObjIdList);
                }

                if (null === $ret) {
                    $artistMusicTitleRow = $this->_artistMusicTitleModel
                        ->findRowById($artistMusicTitleId);
                    $artist = $artistMusicTitleRow->getArtistName();
                    $musicTitle = $artistMusicTitleRow->getMusicTitleName();
                    $this->_logger->debug(
                        'Calling getSimilarSync: ' . $artist
                        . ' - ' . $musicTitle . ' # ' . $type . ' '
                    );
                    $ret = $this->getSimilarSync(
                        $artist, $musicTitle, $type, $extObjIdList
                    );
                }
            } else {
                $ret = array(array(), $extObjIdList);
            }
        } else {
            $completeIdList = array_merge(
                is_array($artistMusicTitleId) ?
                $artistMusicTitleId : array($artistMusicTitleId),
                $similarList,
                $extObjIdList
            );

            $similarityMatrixResponse = $this->_getSimilarityMatrix(
                $completeIdList
            );

            list($similarList, $translationList) = $this->_insertAlbumIds(
                $similarList
            );
            $completeIdList = array_merge(
                array($artistMusicTitleId),
                $similarList,
                $extObjIdList
            );

            $similarityMatrixResponse[0] = $this->_applyListTranslationToMatrix(
                $similarityMatrixResponse[0], $translationList
            );

            $this->_logSparsity($similarityMatrixResponse[0]);
            $similarityMatrixResponse[0] = $this->_enhanceMatrix(
                $similarityMatrixResponse[0]
            );

            $similarityMatrixResponse[0] = $this->_similarityToDissimilarity(
                $similarityMatrixResponse[0]
            );

            $completeIdList = $this->_applyListTranslationToList(
                $completeIdList, $translationList
            );

            $this->_logger->debug(
                "MusicSimilarity::getSimilar local quality{ size: "
                . $similarityMatrixResponse[1] . ". non-zero: "
                . $similarityMatrixResponse[2]
            );

            if (
                ($similarityMatrixResponse[1] < 20 ||
                $similarityMatrixResponse[2] < 0.03) &&
                $mayUseSync
            ) {
                if (is_array($artistMusicTitleId)) {
                    $artistMusicTitleId = min($artistMusicTitleId);
                    $type = 'track';
                }
                $artistMusicTitleRow = $this->_artistMusicTitleModel
                    ->findRowById($artistMusicTitleId);
                $artist = $artistMusicTitleRow->getArtistName();
                $musicTitle = $artistMusicTitleRow->getMusicTitleName();

                $this->_logger->debug(
                    'Calling from lowret getSimilarSync: ' . $artist
                    . ' - ' . $musicTitle . ' # ' . $type . ' '
                );
                $ret = $this->getSimilarSync(
                    $artist, $musicTitle, $type, $extObjIdList
                );
            } else {
                $objList = $this->_fetchObjList($completeIdList);

                // If local information is used, then there must be a task to
                // refresh current data.
                $this->_logger->debug(
                    "MusicSimilarity::getSimilar $artist $musicTitle "
                );
                $this->_taskRequestModel->addTask(
                    'SearchSimilar', $artist, $musicTitle, $type
                );

                $ret = array(
                    $objList,
                    $similarityMatrixResponse[0]
                );
            }
        }

        $this->_logger->debug(
            'MusicSimilarity::getSimilar ret -- '
        );

        if (array_key_exists(0, $ret)) {
            $trackList = array();
            $albumList = array();
            foreach ($ret[0] as $item) {
                if ('album' === $item['type']) {
                    $albumList[] = $item;
                } else {
                    $trackList[] = $item;
                }
            }
            $count = 0;
            $shuffled = array();
            while (count($albumList) || count($trackList)) {
                if ($count % 2 && count($albumList)) {
                    $shuffled[] = array_shift($albumList);
                } elseif (count($trackList)) {
                    $shuffled[] = array_shift($trackList);
                }
                $count++;
            }
            $ret[0] = $shuffled;
        }

        $this->_logger->debug(
            'MusicSimilarity::getSimilar ret end -- '
        );

        $c->stop();
        $this->_logger->debug(
            'MusicSimilarity::getSimilar timimg : ' . $c->get()
        );

        return $ret;
    }

    /**
     * Given a set of ids (amt and album) return the similarity matrix for it.
     */
    public function getSimilarByIds($ids)
    {
        foreach ($ids as $id) {
            if ($id < 0) {
                $albumId = -$id;
                $albumRow = $this->_albumModel->findRowById($albumId);
                $originalTranslationList[$albumId] = $albumRow
                    ->artistMusicTitleIdList;
            }
        }

        $this->_logger->debug("getSimilarByIds ids A ");

        list($similarList, $translationList) = $this->_replaceAlbumIdByAMTIds(
            $ids
        );

        $this->_logger->debug(
            "getSimilarByIds similarList B "
        );


        $similarityMatrix = $this->_getSimilarityMatrix($similarList);

        /*
        list($similarList, $translationList) = $this->_insertAlbumIds(
            $similarList
        );
        */

        $ret = $this->_applyListTranslationToMatrix(
            $similarityMatrix[0], $translationList
        );
        $this->_logSparsity($ret);
        $ret = $this->_enhanceMatrix($ret);

        $ret = $this->_similarityToDissimilarity($ret);


        return $ret;
    }


    /**
     * getSimilarSync The sync version of getSimilar. It just request lastfm,
     * record the results on database and call getSimilar.
     */
    public function getSimilarSync(
        $artist, $musicTitle, $type, $artistMusicTitleIdList = array()
    )
    {
        $c = new DZend_Chronometer();
        $c2 = new DZend_Chronometer();
        $c->start();
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

        $c2->start();

        $rowSet = count($rowSet) > 0 ?
            $this->_artistMusicTitleModel->insertMulti($rowSet) : array();
        $rowSet = count($rowSet) > 0 ?
            $this->_objDb->insertMulti($artistMusicTitleId, $rowSet) : array();

        $c2->stop();

        $ret = $this->getSimilar(
            $artist, $musicTitle, $type, $artistMusicTitleIdList, false
        );

        $c->stop();
        $this->_logger->debug(
            'MusicSimilarity::getSimilarSync time: ' . $c->get()
        );
        $this->_logger->debug(
            'MusicSimilarity::getSimilarSync time c2: ' . $c2->get()
        );


        return $ret;
    }

    protected function _insertAlbumIds($artistMusicTitleIdList)
    {
        $translationList = array();
        if (!empty($artistMusicTitleIdList)) {
            $albumAMTRowSet = $this->_albumHasArtistMusicTitleDb
                ->fetchAllByArtistMusicTitleIdGrouped($artistMusicTitleIdList);

            $albumIdCount = array();
            foreach ($albumAMTRowSet as $row) {
                if (!array_key_exists($row->albumId, $albumIdCount)) {
                    $albumIdCount[$row->albumId] = array();
                }
                $albumIdCount[$row->albumId][] = $row->artistMusicTitleId;
            }
            $freq = array();
            for ($i = 0; $i <= count($artistMusicTitleIdList); $i++) {
                $freq[$i] = array();
            }

            foreach ($albumIdCount as $albumId => $amtList) {
                $freq[count($amtList)][] = $albumId;
            }

            $maxReplacements = (int) count($artistMusicTitleIdList) / 2;

            for ($i = count($artistMusicTitleIdList); $i >= 0; $i--) {
                if ($maxReplacements <= 0) {
                    break;
                }

                foreach ($freq[$i] as $albumId) {
                    $translationList[$albumId] = array();
                    foreach ($albumIdCount[$albumId] as $amtId) {
                        $translationList[$albumId][] = $amtId;
                        if (
                            ($key = array_search(
                                $amtId, $artistMusicTitleIdList
                            ))
                            !== false
                        ) {
                            unset($artistMusicTitleIdList[$key]);
                            $maxReplacements--;
                        }
                    }
                    $artistMusicTitleIdList[] = -$albumId;
                    if ($maxReplacements <= 0) {
                        break;
                    }
                }
            }
        }

        return array($artistMusicTitleIdList, $translationList);
    }

    public function insertMulti($artistMusicTitleId, $rowSet)
    {
        return $this->_objDb->insertMulti($artistMusicTitleId, $rowSet);
    }
}
