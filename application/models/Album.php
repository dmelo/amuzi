<?php

/**
 * Album
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
class Album extends DZend_Model
{
    public function search($q)
    {
        $list = $this->_lastfmModel->searchAlbum($q);
        $ret = [];

        foreach ($list as $item) {
            $album = $this->_lastfmModel->getAlbum(
                $item['musicTitle'], $item['artist']
            );

            $this->_albumDb->insert(
                array(
                    'name' => $album->musicTitle,
                    'cover' => $album->cover,
                    'artist_id'=> $this->_artistModel->insert($album->artist),
                )
            );



            $ret[] = $album;
        }

        return array();
    }

    public function get($artist, $album)
    {
        $artistId = $this->_artistModel->insert($artist);
        $albumRow = $this->_albumDb->findRowByNameAndArtistId($album, $artistId);

        return $albumRow;
    }

    public function insert(LastfmAlbum $album)
    {
        $artistRow = $this->_artistDb->findRowByName($album->artist);
        $id = $this->_albumDb->insert(
            array(
                'name' => $album->name,
                'cover' => $album->cover,
                'artist_id' => $artistRow->id
            )
        );

        $sort = 0;
        foreach ($album->trackList as $track) {
            $artistMusicTitleId = $this->_artistMusicTitleModel->insert(
                $track->artist, $track->musicTitle
            );

            $this->_albumHasArtistMusicTitleDb->insert(
                array(
                    'album_id' => $id,
                    'artist_music_title_id' => $artistMusicTitleId,
                    'sort' => $sort
                )
            );
            $sort++;
        }

        return $id;
    }

    public function findRowById($id)
    {
        return $this->_albumDb->findRowById($id);
    }

    public function findAllFromUser()
    {
        $userListenAlbumRowSet = $this->_userListenAlbumDb
            ->findByUserId($this->_session->user->id);

        $albumIdSet = array();
        foreach ($userListenAlbumRowSet as $userListenAlbumRow) {
            $albumIdSet[] = $userListenAlbumRow->albumId;
        }

        $this->_logger->debug("Album::findAllFromUser - " . print_r($albumIdSet, true));

        return $this->_albumDb->findById($albumIdSet);
    }
}
