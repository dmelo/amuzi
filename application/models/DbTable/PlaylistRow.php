<?php

class DbTable_PlaylistRow extends DZend_Model_DbTableRow
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

    /**
     * addTrack Add a track to this playlist.
     *
     * @param array $trackInfo is a array that have either the keys title, mp3
     * and cover or the key id. The latter is valid just in case the track
     * already exists
     * @return void
     */
    public function addTrack($trackInfo)
    {
        $maxSort = $this->_playlistHasTrackDb->getMaxSort($this->id);
        if (array_key_exists('id', $trackInfo))
            $trackRow = $this->_trackDb->findRowById($trackInfo['id']);
        else
            $trackRow = $this->_trackDb->insert($trackInfo);

        if (null === $trackRow) {
            throw new Zend_Db_Table_Row_Exception('Could not find the track: ' . print_r($trackInfo, true));
        } else {
            $data = array(
                'playlist_id' => $this->id,
                'track_id' => $trackRow->id,
                'sort' => $maxSort + 1);
            $this->_playlistHasTrackDb->insert($data);
        }
    }

    public function rmTrack($url)
    {
        $trackRow = $this->_trackDb->findRowByUrl($url);
        $this->_playlistHasTrackDb->deleteByPlaylistIdAndTrackId(
            $this->id,
            $trackRow->id
        );
    }

    public function deleteSortGreaterThan($sort)
    {
        $this->_playlistHasTrackDb->deleteByPlaylistSortGreaterThan(
            $this->id,
            $sort
        );
    }

    public function getTrackList()
    {
        $trackDb = new DbTable_Track();
        $list = $this->_playlistHasTrackDb->findByPlaylistId($this->id);
        $ret = array();
        foreach ($list as $item) {
            $ret[] = $trackDb->findRowById($item->trackId);
        }

        return $ret;
    }

    public function countTracks()
    {
        return count($this->_playlistHasTrackDb->findByPlaylistId($this->id));
    }

    public function getCover()
    {
        $select = $this->_trackDb->select()->from(
            array('p' => 'playlist'), array()
        );
        $select->join(
            array('pht' => 'playlist_has_track'),
            'p.id = pht.playlist_id',
            array()
        );
        $select->join(
            array('t' => 'track'), 't.id = pht.track_id', array('cover')
        );
        $select->where(
            $this->_playlistDb->getAdapter()->quoteInto(
                't.cover is not null and p.id = ?',
                $this->id
            )
        );

        $row = $this->_playlistDb->fetchRow($select);
        return $row ? $row->cover : '/img/playlist64.png';
    }
}
