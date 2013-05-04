<?php

/**
 * DbTable_ArtistMusicTitleRow
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
class DbTable_ArtistMusicTitleRow extends DZend_Db_Table_Row
{
    public function getArtistName()
    {
        $name = null;
        try {
            $artistList = Zend_Registry::get('artistList');
        } catch (Zend_Exception $e) {
            $artistList = array();
        }

        if (!array_key_exists($this->artistId, $artistList)) {
            $artistDb = new DbTable_Artist();
            if (($artistRow = $artistDb->findRowById($this->artistId))
                !== null) {
                $artistList[$this->artistId] = $artistRow;
                Zend_Registry::set('artistList', $artistList);
            }
        }
        $name = $artistList[$this->artistId]->name;

        return $name;
    }

    public function getMusicTitleName()
    {
        $name = null;
        $musicTitleDb = new DbTable_MusicTitle();
        if (($musicTitleRow = $musicTitleDb->findRowById($this->musicTitleId))
            !== null) {
            $name = $musicTitleRow->name;
        }

        return $name;
    }

    public function getCover()
    {
        $cover = null;
        $musicTrackLinkModel = new MusicTrackLink();
        if (($trackRow = $musicTrackLinkModel->getTrackById($this->id))
            !== null) {
            $cover = $trackRow->cover;
        }

        return $cover;
    }
}
