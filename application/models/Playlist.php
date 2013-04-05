<?php

/**
 * Playlist Represents a set of tracks on a given order.
 *
 * @package amuzi
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
class Playlist extends DZend_Model
{
    /**
     * create Verify if a playlist doesn't exist and creates it.
     *
     * @param string $name Playlist's name.
     * @param boolean $public Says whether this playlist must be public or not.
     * @return DbTable_PlaylistRow Returns the playlist owned by the logged.
     * user which name maches the playlist's, or null if no user is logged.
     */
    public function create($name, $public = 'public')
    {
        $ret = null;
        if (isset($this->_session->user)) {
            $ret = $this->_playlistDb->findRowByUserIdAndName(
                $this->_session->user->id,
                $name
            );
            if (!$ret) {
                try {
                    $ret = $this->_playlistDb->create(
                        $this->_session->user->id, $name, $public
                    );
                } catch(Zend_Db_Table_Exception $e) {
                    $this->_logger->info($e->getMessage());
                    throw new Zend_Exception("the playlist is not yours");
                }
            }
        }

        return $ret;
    }

    /**
     * import Import playlist from jPlayer.playlist format to database.
     *
     * @param mixed $playlist Playlist on jplaylist's format.
     * @param mixed $name Playlist's name.
     * @return void
     */
    public function import($playlist, $name = 'default')
    {
        $playlistRow = $this->create($name);
        $sort = 0;
        foreach ($playlist as $track) {
            $playlistRow->setTrack($track, $sort);
            $sort++;
        }
        $playlistRow->deleteSortGreaterThan($sort - 1);
    }

    /**
     * export Exports the playlist from the database to the jplaylist's format.
     *
     * @param mixed $name Playlist's name.
     * @return array List of tracks on jplaylist's format.
     */
    public function export($name)
    {
        $ret = null;
        $playlistRow = null;
        $this->_logger->debug("export: " . $name);
        $this->_logger->debug("export: " . gettype($name));
        $user = $this->_session->user;
        $this->_logger->debug("export: " . $user->currentPlaylistId);
        if (gettype($name) === 'string') {
            if ('' === $name) { // if current is null, ret will be null.
                if (null !== $user->currentPlaylistId) {
                    $playlistRow = $this->_playlistDb->findRowById(
                        $user->currentPlaylistId
                    );
                }
            } else {
                $playlistRow = $this->_playlistDb->findRowByUserIdAndName(
                    $user->id, $name
                );
            }
        } elseif (gettype($name) === 'integer') {
            $playlistRow = $this->_playlistDb->findRowById($name);
            if ($playlistRow->userId !== $user->id
                    && 'public' !== $playlistRow->privacy)
                $playlistRow = null;
            else
                $this->_userListenPlaylistModel->addUserPlaylist($playlistRow);
        }

        $this->_logger->debug(print_r($playlistRow, true));
        $this->_logger->debug($playlistRow->id);

        if (null !== $playlistRow) {
            $ret = array();
            $user->currentPlaylistId = $playlistRow->id;
            $user->currentAlbumId = null;
            $user->save();
            $trackList = $playlistRow->getTrackListAsArray();
            $this->_logger->debug(count($trackList));
            $ret = array(
                $trackList,
                $playlistRow->name,
                $playlistRow->repeat,
                $playlistRow->shuffle,
                $playlistRow->currentTrack
            );
        }

        $this->_logger->debug(print_r($ret, true));

        return $ret;
    }

    /**
     * setRepeat Set a playlist to (or not to) loop the tracks.
     *
     * @param string $name Playlist's name
     * @param string $repeat "true" for repeating, any other string otherwise.
     * @return boolean Returns true if the adjustment was successful, false
     * otherwise.
     */
    public function setRepeat($name, $repeat)
    {
        try {
            $playlistRow = $this->_playlistDb->findRowByUserIdAndName(
                $this->_session->user->id, $name
            );
            $playlistRow->repeat = "true" == $repeat ? 1 : 0;
            $playlistRow->save();
            return true;
        } catch(Zend_Exception $e) {
            return false;
        }
    }

    /**
     * setShuffle Set a playlist to (or not to) shuffle the tracks.
     *
     * @param string $name Playlist's name
     * @param string $shuffle "true" for shuffling, any other string otherwise.
     * @return boolean Returns true if the adjustment was successful, false
     * otherwise.
     */
    public function setShuffle($name, $shuffle)
    {
        try {
            $playlistRow = $this->_playlistDb->findRowByUserIdAndName(
                $this->_session->user->id, $name
            );
            $playlistRow->shuffle = "true" == $shuffle ? 1 : 0;
            $playlistRow->save();
            return true;
        } catch(Zend_Exception $e) {
            return false;
        }
    }

    /**
     * findRowByName Find playlist by the given name, owner by the current
     * logged in user.
     *
     * @param string $name Playlist's name.
     * @return void Return the corresponding Zend_Db_Table_Row or null, if none
     * is found.
     */
    public function findRowByName($name)
    {
        return $this->_playlistDb->findRowByUserIdAndName(
            $this->_session->user->id, $name
        );
    }

    /**
     * findRowById Find a row, given it's ID.
     *
     * @param int $id Row ID
     * @return Returns the asked row.
     */
    public function findRowById($id)
    {
        return $this->_playlistDb->findRowByUserIdAndId(
            $this->_session->user->id, $id
        );
    }

    /**
     * setNewName
     *
     * @param string $name Current playlist's name.
     * @param string $newName New playlist's name.
     * @return boolean Return true if the operation was successful, false
     * otherwise.
     */
    public function setNewName($name, $newName)
    {
        try {
            $playlistRow = $this->findRowByName($name);
            if(null === $playlistRow)
                return false;
            $playlistRow->name = $newName;
            $playlistRow->save();
            return true;
        } catch(Zend_Exception $e) {
            return false;
        }
    }

    /**
     * rmTrack Remove a track from a playlist.
     *
     * @param $trackId Track ID
     * @param string $name
     * @return void
     */
    public function rmTrack($trackId, $name = 'default')
    {
        $playlistRow = $this->create($name);
        $playlistRow->rmTrack($trackId);
    }

    /**
     * setCurrentTrack Set a track as the currently played track.
     *
     * @param string $name Playlist's name
     * @param int $current Track position on playlist.
     * @return void
     */
    public function setCurrentTrack($name, $current)
    {
        $row = $this->_playlistDb->findRowByUserIdAndName(
            $this->_session->user->id, $name
        );
        $row->currentTrack = $current;
        $row->save();
    }

    /**
     * fetchAllUsers get all user's rows.
     *
     * @return void
     */
    public function fetchAllUsers()
    {
        return $this->_playlistDb->fetchAllUsers();
    }

    /**
     * search Search for playlists owned by the current user.
     *
     * @param string $q Name (or part of it) of the playlist
     * @return Zend_Db_Table_Rowset List of user's playlist that matches the
     * search.
     */
    public function search($q)
    {
        return $this->_playlistDb->search($q);
    }

    /**
     * remove Remove a user's playlist with all the playlist_has_track records.
     *
     * @param mixed $name Playlis's name.
     * @return bool Returns true if the playlist was successfully deleted,
     * the error string otherwise.
     */
    public function remove($id)
    {
        try {
            $playlistRow = $this->_playlistDb->findRowByUserIdAndId(
                $this->_session->user->id, $id
            );

            if (null !== $playlistRow) {
                // If its going to delete the current playlist, then it must
                // first be removed from user->current_playlist_id
                if ($this->_session->user->currentPlaylistId ===
                        $playlistRow->id) {
                    $this->_session->user->currentPlaylistId = null;
                    $this->_session->user->save();
                }

                // If there is users who listen to this playlist, those records 
                // must be deleted first.
                $this->_userListenPlaylistModel->deleteByPlaylistId(
                    $playlistRow->id
                );

                $this->_playlistHasTrackDb->deleteByPlaylistId(
                    $playlistRow->id
                );
                $playlistRow->delete();

                // If the default playlist is deleted, then choose another one
                // to be the current_playlist_id.
                if (null === $this->_session->user->current_playlist_id) {
                    $newPlaylistRow = $this->_playlistDb->findRowByUserId(
                        $this->_session->user->id
                    );
                    $this->_session->user->current_playlist_id =
                        $newPlaylistRow->id;
                    $this->_session->user->save();
                }
                return true;
            } else {
                return "Playlist not found";
            }

        } catch(Zend_Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Changes the privacy behavior of a user's playlist.
     *
     * @param $name string The name of the playlist.
     * @param $public string whether it is public or private.
     *
     * @return boolean Returns true if the change well succeded, false
     * otherwise.
     */
    public function setPublic($name, $privacy)
    {
        try {
            $playlistRow = $this->_playlistDb->findRowByUserIdAndName(
                $this->_session->user->id, $name
            );
            $playlistRow->privacy = $privacy;
            $playlistRow->save();
            return true;
        } catch(Zend_Exception $e) {
            return false;
        }
    }

    public function getCurrentRow()
    {
        return $this->_playlistDb->findRowById(
            $this->_session->user->currentPlaylistId
        );
    }
}
