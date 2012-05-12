<?php

/**
 * Playlist Represents a set of tracks on a given order.
 *
 * @package amuzi
 * @version 1.0
 * @copyright Copyright (C) 2010 Diogo Oliveira de Melo. All rights reserved.
 * @author Diogo Oliveira de Melo <dmelo87@gmail.com>
 * @license GPL version 3
 */
class Playlist extends DZend_Model
{
    protected $_playlistDb;
    protected $_playlistHasTrackDb;
    protected $_userListenPlaylistModel;
    protected $_session;

    public function __construct()
    {
        parent::__construct();
        $this->_playlistDb = new DbTable_Playlist();
        $this->_playlistHasTrackDb = new DbTable_PlaylistHasTrack();
        $this->_userListenPlaylistModel = new UserListenPlaylist();
        $this->_session = DZend_Session_Namespace::get('session');
    }

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
        $user = $this->_session->user;
        if (gettype($name) === 'string') {
            if ('' === $name)
                $playlistRow = $this->_playlistDb->findRowById(
                    $user->currentPlaylistId
                );
            else
                $playlistRow = $this->_playlistDb->findRowByUserIdAndName(
                    $user->id, $name
                );
        } elseif (gettype($name) === 'integer') {
            $playlistRow = $this->_playlistDb->findRowById($name);
            if ($playlistRow->user_id !== $user->id && 'public' !== $playlistRow->privacy)
                $playlistRow = null;
            else
                $this->_userListenPlaylistModel->addUserPlaylist($playlistRow);
        }

        $ret = null;
        if (null !== $playlistRow) {
            $ret = array();
            $user->currentPlaylistId = $playlistRow->id;
            $user->save();
            $trackList = $playlistRow->getTrackList();
            foreach ($trackList as $track) {
                $ret[] = $track->toArray();
            }
            $ret = array(
                $ret,
                $playlistRow->name,
                $playlistRow->repeat,
                $playlistRow->shuffle,
                $playlistRow->currentTrack
            );
        }

        return $ret;
    }

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
     * addTrack Add a track into the playlist
     *
     * @param array $trackInfo Track information
     * @param string $name Playlist's name
     * @return Returns the track row.
     */
    public function addTrack(array $trackInfo, $name = 'default')
    {
        $playlistRow = $this->create($name);
        return $playlistRow->addTrack($trackInfo);
    }

    /**
     * rmTrack Remove a track from a playlist.
     *
     * @param $url Track's url
     * @param string $name
     * @return void
     */
    public function rmTrack($url, $name = 'default')
    {
        $playlistRow = $this->create($name);
        $playlistRow->rmTrack($url);
    }

    public function setCurrentTrack($name, $current)
    {
        $row = $this->_playlistDb->findRowByUserIdAndName(
            $this->_session->user->id, $name
        );
        $row->currentTrack = $current;
        $row->save();
    }

    public function fetchAllUsers()
    {
        return $this->_playlistDb->fetchAllUsers();
    }

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
    public function remove($name)
    {
        try {
            $playlistRow = $this->_playlistDb->findRowByUserIdAndName(
                $this->_session->user->id, $name
            );

            if (null !== $playlistRow) {
                // If it going to delete the current playlist, then it must
                // first be removed from user->current_playlist_id
                if ($this->_session->user->currentPlaylistId ===
                        $playlistRow->id) {
                    $this->_session->user->currentPlaylistId = null;
                    $this->_session->user->save();
                }

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
     * findRowById Find a row, given it's ID.
     *
     * @param int $id Row ID
     * @return Returns the asked row.
     */
    public function findRowById(int $id)
    {
        return $this->_playlistDb->findById($id);
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
}
