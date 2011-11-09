<?php

class Playlist
{
    protected $_playlistDb;
    protected $_session;

    public function __construct()
    {
        $this->_playlistDb = new DbTable_Playlist();
        $this->_session = new Zend_Session_Namespace('session');
    }

    /**
     * create Verify if a playlist doesn't exist and creates it.
     *
     * @param string $name Playlist's name.
     * @return DbTable_Playlist_Row Returns the playlist owned by the logged 
     * user which name maches the playlist's, or null if no user is logged.
     */
    public function create($name)
    {
        $ret = null;
        if(isset($this->_session->user)) {
            $ret = $this->_playlistDb->findByUserIdAndName($this->_session->user->id, $name);
            if(!$ret) {
                $this->_playlistDb->create($this->_session->user->id, $name);
            }
        }

        return $ret;
    }

    public function import($playlist, $name)
    {
        $playlistRow = $this->create($name);
        //$playlistRow->setTrack($trackInfo, $order);
    }
}
