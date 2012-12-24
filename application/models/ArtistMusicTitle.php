<?php

/**
 * ArtistMusicTitle
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
class ArtistMusicTitle extends DZend_Model
{
    public function insert($artist, $musicTitle)
    {
        $artistId = $this->_artistModel->insert($artist);
        $musicTitleId = $this->_musicTitleModel->insert($musicTitle);

        return $this->_artistMusicTitleDb->insert(
            array(
                'artist_id' => $artistId,
                'music_title_id' => $musicTitleId
            )
        );
    }

    public function findByArtistAndMusicTitle($artist, $musicTitle)
    {
        $db = $this->_artistMusicTitleDb->getAdapter();
        $where = $db->quoteInto(
            'artist_id in (select id from artist where name = ?)', $artist
        ) . $db->quoteInto(
            ' AND music_title_id in (select id from music_title where '
            . 'name = ?)', $musicTitle
        );
        return $this->_artistMusicTitleDb->fetchRow($where);
    }

    public function fetchAllArtistAndMusicTitle($idsList)
    {
        return $this->_artistMusicTitleDb->fetchAllArtistAndMusicTitle(
            $idsList
        );
    }

    public function autocomplete($q)
    {
        return $this->_artistMusicTitleDb->autocomplete($q);
    }

    public function getBestGuess($q)
    {
        $list = $this->autocomplete($q);
        foreach ($list as $item) {
            return $item;
        }

        return null;
    }
}
