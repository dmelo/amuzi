<?php

class Playlist
{
    protected $_playlistDb;
    protected $_session;

    public function __construct()
    {
        $this->_playlistDb = new DbTable_Playlist();
        $this->_session = Diogo_Session_Namespace::get('session');
    }

    /**
     * create Verify if a playlist doesn't exist and creates it.
     *
     * @param string $name Playlist's name.
     * @return DbTable_Playlist_Row Returns the playlist owned by the logged.
     * user which name maches the playlist's, or null if no user is logged.
     */
    public function create($name)
    {
        $ret = null;
        if(isset($this->_session->user)) {
            $ret = $this->_playlistDb->findRowByUserIdAndName($this->_session->user->id, $name);
            if(!$ret) {
                $this->_playlistDb->create($this->_session->user->id, $name);
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
        foreach($playlist as $track) {
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
        if('' === $name)
            $playlistRow = $this->_playlistDb->findRowById($user->current_playlist_id);
        else
            $playlistRow = $this->_playlistDb->findRowByUserIdAndName($user->id, $name);
        $user->current_playlist_id = $playlistRow->id;
        $user->save();
        $trackList = $playlistRow->getTrackList();
        $ret = array();
        foreach($trackList as $track) {
            $ret[] = array('title' => $track->title,
                'mp3' => $track->url);
        }

        $ret = array($ret, $playlistRow->name, $playlistRow->repeat, $playlistRow->shuffle, $playlistRow->current_track);

        return $ret;
    }

    public function setRepeat($name, $repeat)
    {
        try {
            $playlistRow = $this->_playlistDb->findRowByUserIdAndName($this->_session->user->id, $name);
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
            $playlistRow = $this->_playlistDb->findRowByUserIdAndName($this->_session->user->id, $name);
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
     * @return void
     */
    public function addTrack(array $trackInfo, $name = 'default')
    {
        $playlistRow = $this->create($name);
        $playlistRow->addTrack($trackInfo);
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
        $row = $this->_playlistDb->findRowByUserIdAndName($this->_session->user->id, $name);
        $row->current_track = $current;
        $row->save();
    }
}
