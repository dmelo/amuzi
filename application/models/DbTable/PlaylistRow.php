<?php

class DbTable_PlaylistRow
{
    public function setTrack($trackInfo, $order)
    {
        // Make sure trackInfo is on the database and retrieve it's row.
        $trackDb = new DbTable_Track();
        $trackRow = $trackDb->insert($trackInfo);


        // Set the right order and bound between the track and the playlist.
    }
}
