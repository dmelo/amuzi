<?php

class DbTable_PlaylistHasTrack extends Diogo_Model_DbTable
{
    protected $_name = 'playlist_has_track';
    protected $_primary = 'id';
    protected $_rowClass = 'DbTable_PlaylistHasTrackRow';

    public function findByPlaylistAndSort($playlistId, $sort)
    {
        $where = $this->_db->quoteInto("playlist_id = ? AND ", $playlistId) . $this->_db->quoteInto("sort = ?", $sort);
        return $this->fetchRow($where);
    }

    public function findByPlaylist($playlistId)
    {
        $where = $this->_db->quoteInto("playlist_id = ?", $playlistId);
        return $this->fetchAll($where, 'sort');
    }

    public function insert($data)
    {
        $row = $this->findByPlaylistAndSort($data['playlist_id'], $data['sort']);
        if(!$row) {
            parent::insert($data);
            $row = $this->findByPlaylistAndSort($data['playlist_id'], $data['sort']);
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

    /**
     * getMaxSort Get the highiest sort on a playlist.
     *
     * @param mixed $playlistId Playlist ID;
     * @return int the max sort.
     */
    public function getMaxSort($playlistId)
    {
        $select = new Zend_Db_Select($this->_db);
        $select->from('playlist_has_track', array('max' => Zend_Db_Expr('max(sort)')))->where($this->_db->quoteInto('playlist_id = ?', $playlistId));

        return $this->fetchRow($select)->max;
    }
}
