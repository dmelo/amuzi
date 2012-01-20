<?php

class DbTable_PlaylistRow extends Zend_Db_Table_Row
{
    protected $_playlistDb;
    protected $_playlistHasTrackDb;
    protected $_trackDb;

    public function __construct(array $config = array())
    {
        parent::__construct($config);
        $this->_playlistDb = new DbTable_Playlist();
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

    public function rmTrack($url)
    {
        $trackRow = $this->_trackDb->findRowByUrl($url);
        $this->_playlistHasTrackDb->deleteByPlaylistIdAndTrackId($this->id, $trackRow->id);
    }

    public function deleteSortGreaterThan($sort)
    {
        $this->_playlistHasTrackDb->deleteByPlaylistSortGreaterThan($this->id, $sort);
    }

    public function getTrackList()
    {
        $trackDb = new DbTable_Track();
        $list = $this->_playlistHasTrackDb->findByPlaylistId($this->id);
        $ret = array();
        foreach($list as $item) {
            $ret[] = $trackDb->findRowById($item->track_id);
        }

        return $ret;
    }

    public function countTracks()
    {
        return count($this->_playlistHasTrackDb->findByPlaylistId($this->id));
    }

    public function getCover()
    {
        $select = $this->_trackDb->select()->from(array('playlist' => 'p', 'playlist_has_track' => 'pht', 'track' => 't'), array('t.cover'))
            ->where('p.id = pht.playlist_id AND pht.track_id = t.id AND t.cover is not NULL and p.id = ?',
            $this->id)->order('pht.sort');

        $row = $this->_playlistDb->fetchRow($select);
        if($row)
            return $row->cover;
        else
            return null;
    }
}
