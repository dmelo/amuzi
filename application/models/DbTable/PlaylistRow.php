<?php

class DbTable_PlaylistRow extends Zend_Db_Table_Row
{
    protected $_playlistHasTrackDb;
    protected $_trackDb;

    public function __construct(array $config = array())
    {
        parent::__construct($config);
        $this->playlistHasTrackDb = new DbTable_PlaylistHasTrack();
        $this->trackDb = new DbTable_Track();
    }
    public function setTrack($trackInfo, $sort)
    {
        // Make sure trackInfo is on the database and retrieve it's row.
        $trackDb = new DbTable_Track();
        $trackRow = $trackDb->insert($trackInfo);

        // Set the right order and bound between the track and the playlist.
        $playlistHasTrackDb = new DbTable_PlaylistHasTrack();
        $data = array(
            'playlist_id' => $this->id,
            'track_id' => $trackRow->id,
            'sort' => $sort);

        $playlistHasTrackDb->insert($data);
    }

    public function addTrack($trackInfo)
    {
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
