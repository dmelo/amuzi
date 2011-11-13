<?php

class DbTable_PlaylistHasTrack extends Zend_Db_Table_Abstract
{
    protected $_name = 'playlist_has_track';
    protected $_primary = 'id';
    protected $_rowClass = 'DbTable_PlaylistHasTrackRow';

    public function findByPlaylistAndSort($playlistId, $sort)
    {
        $db = $this->getAdapter();
        $where = $db->quoteInto("playlist_id = ? AND ", $playlistId) . $db->quoteInto("sort = ?", $sort);
        return $this->fetchRow($where);
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
        $db = $this->getAdapter();
        $where = $db->quoteInto("playlist_id = ? AND ", $playlistId) . $db->quoteInto("sort > ?", $sort);
        $this->delete($where);
    }
}
