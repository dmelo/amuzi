<?php

class DbTable_PlaylistRow extends Zend_Db_Table_Row
{
    protected $_playlistHasTrackDb;
    protected $_trackDb;

    public function __construct(array $config = array())
    {
        parent::__construct($config);
        $this->_playlistHasTrackDb = new DbTable_PlaylistHasTrack();
        $this->_trackDb = new DbTable_Track();
    }
    public function setTrack($trackInfo, $sort)
    {
        // Make sure trackInfo is on the database and retrieve it's row.
        $trackRow = $this->_trackDb->insert($trackInfo);

        // Set the right order and bound between the track and the playlist.
        $data = array(
            'playlist_id' => $this->id,
            'track_id' => $trackRow->id,
            'sort' => $sort);

        $this->_playlistHasTrackDb->insert($data);
    }

    public function addTrack($trackInfo)
    {
        $maxSort = $this->_playlistHasTrackDb->getMaxSort($this->id);
        $trackRow = $this->_trackDb->insert($trackInfo);
        $data = array(
            'playlist_id' => $this->id,
            'track_id' => $trackRow->id,
            'sort' => $maxSort + 1);
        $this->_playlistHasTrackDb->insert($data);
    }

    public function deleteSortGreaterThan($sort)
    {
        $playlistHasTrackDb = new DbTable_PlaylistHasTrack();
        $playlistHasTrackDb->deleteByPlaylistSortGreaterThan($this->id, $sort);
    }

    public function getTrackList()
    {
        $playlistHasTrackDb = new DbTable_PlaylistHasTrack();
        $trackDb = new DbTable_Track();
        $list = $playlistHasTrackDb->findByPlaylist($this->id);
        $ret = array();
        foreach($list as $item) {
            $ret[] = $trackDb->findById($item->track_id);
        }

        return $ret;
    }
}
