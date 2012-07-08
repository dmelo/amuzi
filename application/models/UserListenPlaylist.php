<?php

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
}
