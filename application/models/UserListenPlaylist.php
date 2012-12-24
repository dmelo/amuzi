<?php

/**
 * UserListenPlaylist
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
/**
 * UserListenPlaylist Tracks playlists that is shared to some user but not
 * owned by him.
 *
 * @package amuzi
 * @version 1.0
 * @copyright Copyright (C) 2010 Diogo Oliveira de Melo. All rights reserved.
 * @author Diogo Oliveira de Melo <dmelo87@gmail.com>
 * @license GPL version 3
 */
class UserListenPlaylist extends DZend_Model
{
    /**
     * addUserPlaylist Add an user as being the listener of a foreign playlist. 
     * It will check for playlist and user existence and will only complete the 
     * operation if the playlist is set to be public.
     *
     * @param mixed $playlistRow Playlist row
     * @param mixed $userRow User row, if null is given, will assume the
     * currently logged in user.
     * @return void Returns the new row ID in case of success, false otherwise.
     */
    public function addUserPlaylist($playlistRow, $userRow = null)
    {
        if (null === $userRow)
            $userRow = $this->_session->user;

        if ($userRow instanceOf DbTable_UserRow &&
            $playlistRow instanceOf DbTable_PlaylistRow) {
            if ('public' === $playlistRow->privacy) {
                $data = array(
                    'user_id' => $userRow->id,
                    'playlist_id' => $playlistRow->id
                );

                try {
                    return $this->_userListenPlaylistDb->insert($data);
                } catch (Zend_Db_Statement_Exception $e) {
                    $row = $this->_userListenPlaylistDb->
                        findRowByUserIdAndPlaylistId(
                            $data['user_id'], $data['playlist_id']
                        );
                    return $row->id;
                }
            }
        }

        return false;
    }

    public function deleteByPlaylistId($playlistId)
    {
        $this->_userListenPlaylistDb->deleteByPlaylistId($playlistId);
    }
}
