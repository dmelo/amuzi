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
        $playlistDb = new DbTable_Playlist();
        $session = new Zend_Session_Namespace('session');
        $user = $session->user;
        $playlistRow = $playlistDb->findRowByUserIdAndName($user->id, $name);
        $trackList = $playlistRow->getTrackList();
        $ret = array();
        foreach($trackList as $track) {
            $ret[] = array('title' => $track->title,
                'mp3' => $track->url);
        }

        return $ret;
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
     * @param int $url
     * @param string $name
     * @return void
     */
    public function rmTrack($sort, $name = 'default')
    {
        $playlistRow = $this->create($name);
        $playlistRow->rmTrack($sort);
    }
}
