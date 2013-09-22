<?php

/**
 * ArtistSimilarity
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
class ArtistSimilarity extends DZend_Model
{
    public function insertSimilarities($artistId, $similarityList)
    {
        $this->_logger->debug(
            "ArtistSimilarity::insertSimilarities $artistId, "
            . print_r($similarityList, true)
        );
        $artistIdSet = array();
        foreach ($similarityList as $artist) {
            $artistIdSet[] = $this->_artistModel->insert(
                $artist['name'], $artist['cover']
            );
        }


        $this->_logger->debug(
            "ArtistSimilarity::insertSimilarities $artistId, "
            . print_r($artistIdSet, true)
        );

        $fArtistId = 0;
        $sArtistId = 0;
        foreach ($artistIdSet as $artistIdB) {
            $ret[] = $artistIdB;
            list($fArtistId, $sArtistId) = $artistId < $artistIdB ?
                array($artistId, $artistIdB):
                array($artistIdB, $artistId);

            try {
                $this->_objDb->insert(
                    array(
                        'f_artist_id' => $fArtistId,
                        's_artist_id' => $sArtistId
                    )
                );
            } catch (Zend_Db_Statement_Exception $e) {
            }
        }

        return $this->_artistModel->findById($artistIdSet);
    }
}
