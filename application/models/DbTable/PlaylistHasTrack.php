<?php

class DbTable_PlaylistHasTrack extends Diogo_Model_DbTable
{
    protected $_name = 'playlist_has_track';
    protected $_primary = 'id';
    protected $_rowClass = 'DbTable_PlaylistHasTrackRow';

    public function insert($data)
    {
        $row = $this->findRowByPlaylistIdAndSort($data['playlist_id'], $data['sort']);
        if(!$row) {
            parent::insert($data);
            $row = $this->findRowByPlaylistIdAndSort($data['playlist_id'], $data['sort']);
        }
        elseif($row->track_id != $data['track_id']) {
            $row->track_id = $data['track_id'];
            $row->save();
        }

        return $row;
    }

    public function deleteByPlaylistSortGreaterThan($playlistId, $sort)
    {
        $where = $this->_db->quoteInto("playlist_id = ? AND ", $playlistId) . $this->_db->quoteInto("sort > ?", $sort);
        $this->delete($where);
    }

    public function deleteByPlaylistAndSort($playlistId, $sort)
    {
        $where = $this->_db->quoteInto('playlist_id = ?', $playlistId) . $this->_db->quoteInto(' AND sort = ?', $sort);
        $this->delete($where);

        // TODO: try to optimize this to something like update set sort = sort
        // - 1 where sort > $sort. It works on the mysql console but not on
        // Zend.
        for($i = $sort + 1; $i <= $this->getMaxSort($playlistId); $i++) {
            $set = array('sort' => $i - 1);
            $where = $this->_db->quoteInto('playlist_id = ?', $playlistId) . $this->_db->quoteInto(' AND sort = ?', $i);
            $this->update($set, $where);
        }
    }

    /**
     * getMaxSort Get the highiest sort on a playlist.
     *
     * @param mixed $playlistId Playlist ID;
     * @return int the max sort.
     */
    public function getMaxSort($playlistId)
    {
        $select = $this->select()->from('playlist_has_track', array('max(sort) as max'))->where($this->_db->quoteInto('playlist_id = ?', $playlistId));

        return $this->fetchRow($select)->max;
    }
}
