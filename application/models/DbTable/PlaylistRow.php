<?php

class DbTable_PlaylistRow extends Zend_Db_Table_Row
{
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

    public function deleteSortGreaterThan($sort)
    {
        $playlistHasTrackDb = new DbTable_PlaylistHasTrack();
        $playlistHasTrackDb->deleteByPlaylistSortGreaterThan($this->id, $sort);
    }
}
