<?php

/**
 * DbTable_AlbumRow
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
class DbTable_AlbumRow extends DZend_Db_Table_Row implements DbTable_iTrackCollectionRow
{
    public function getArray()
    {
        $columns = array(
            'id',
            'name',
            'cover',
            'artist',
            'trackList'
        );

        $ret = array();
        foreach($columns as $column) {
            $ret[$column] = $this->$column;
        }

        return $ret;
    }

    public function getTrackListAsArray()
    {
        return $this->trackList;
    }

    public function playTime()
    {
        $time = 0;
        foreach ($this->trackList as $trackRow) {
            $time += $trackRow['duration'];
        }

        return $time;
    }

    public function getCover()
    {
        return $this->cover;
    }

    public function getType()
    {
        return 'album';
    }

    public function __get($name)
    {
        if ('artist' === $name) {
            $artistDb = new DbTable_Artist();
            $artistRow = $artistDb->findRowById($this->artistId);
            return $artistRow->name;
        } elseif ('trackList' === $name) {
            $musicTrackLinkModel = new MusicTrackLink();
            $ret = array();
            $albumHasArtistMusicTitleDb = new DbTable_AlbumHasArtistMusicTitle();
            $ahamtRowset = $albumHasArtistMusicTitleDb->findByAlbumId($this->id);
            $artistMusicTitleModel = new ArtistMusicTitle();

            foreach ($ahamtRowset as $row) {
                $trackRow = $musicTrackLinkModel->getTrackById($row->artistMusicTitleId);
                if (null === $trackRow) {
                    $track = $artistMusicTitleModel->findArtistAndMusicTitleById($row->artistMusicTitleId);
                } else {
                    $track = $trackRow->getArray();
                }

                $ret[] = $track;
            }

            return $ret;
        } else {
            return parent::__get($name);
        }
    }
}
