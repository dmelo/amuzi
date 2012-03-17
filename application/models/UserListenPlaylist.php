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
class UserListenPlaylist
{
    protected $_userListenPlaylistDb;
    protected $_userDb;
    protected $_playlistDb;
    protected $_session;

    public function __construct()
    {
        $this->_userListenPlaylistDb = new DbTable_UserListenPlaylist();
        $this->_userDb = new DbTable_User();
        $this->_playlistDb = new DbTable_Playlist();
        $this->_session = DZend_Session_Namespace::get('session');
    }

    /**
     * addUserPlaylist Add an user as being the listener of a foreign playlist. 
     * It will check for playlist and user existence and will only complete the 
     * operation if the playlist is set to be public.
     *
     * @param mixed $playlistId Playlist ID
     * @param mixed $userId User ID, if null is given, will assume the
     * currently logged in user.
     * @return void Returns the new row ID in case of success, false otherwise.
     */
    public function addUserPlaylist($playlistId, $userId = null)
    {
        $userRow = null;
        if (null === $userId)
            $userRow = $this->_session->user;
        else
            $userRow = $this->_userDb->findRowById($userId);

        if (null !== $userRow) {
            $playlistRow = $this->_playlistDb->findRowById($playlistId);
            if(null != $playlistRow && 'public' === $playlistRow->privacy) {
                $data = array(
                    'user_id' => $userRow->id,
                    'playlist_id' => $playlistRow->id
                );

                return $this->_userListenPlaylistDb->insert($data);
            }
        }

        return false;
    }
}
