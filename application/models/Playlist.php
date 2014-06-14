<?php

/**
 * Playlist Represents a set of tracks on a given order.
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
        if ($this->_getUserRow() !== null) {
            $ret = $this->_playlistDb->findRowByUserIdAndName(
                $this->_getUserId(),
                $name
            );
            if (!$ret) {
                try {
                    $ret = $this->_playlistDb->create(
                        $this->_getUserId(), $name, $public
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
     * @param string $ext Extension of the tracks (mp3, mp4, flv..). Default is flv.
     * @return array List of tracks on jplaylist's format.
     */
    public function export($id)
    {
        $id = (int) $id;
        $playlistId = null;
        $ret = null;
        $playlistRow = null;
        $user = $this->_getUserRow();
        $this->_logger->debug('Playlist ---- ' . $id . '#' . gettype($id));

        if (0 === $id && null !== $user->currentPlaylistId) {
            $playlistId = $user->currentPlaylistId;
        } else {
            $playlistId = $id;
        }
        $playlistRow = $this->_playlistDb->findRowById($playlistId);

        // Prevent from accessing a playlist which he doesn't have access.
        if (null !== $playlistRow && $playlistRow->userId != $user->id
            && $playlistRow->privacy != 'public') {
            $playlistRow = null;
        }

        if (null !== $playlistRow && null !== $playlistId) {
            $user->currentPlaylistId = $playlistId;
            $user->currentAlbumId = null;
            $user->save();
            $ret = $playlistRow->export();
        }

        return $ret;
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
                $this->_getUserId(), $name
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
            $this->_getUserId(), $name
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
            $this->_getUserId(), $id
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
            $this->_getUserId(), $name
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
                $this->_getUserId(), $id
            );

            if (null !== $playlistRow) {
                // If its going to delete the current playlist, then it must
                // first be removed from user->current_playlist_id
                if ($this->_getUserRow()->currentPlaylistId ===
                        $playlistRow->id) {
                    $this->_getUserRow()->currentPlaylistId = null;
                    $this->_getUserRow()->save();
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
                if (null === $this->_getUserRow()->current_playlist_id) {
                    $newPlaylistRow = $this->_playlistDb->findRowByUserId(
                        $this->_getUserId()
                    );
                    $this->_getUserRow()->current_playlist_id =
                        $newPlaylistRow->id;
                    $this->_getUserRow()->save();
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
                $this->_getUserId(), $name
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
            $this->_getUserRow()->currentPlaylistId
        );
    }
}
