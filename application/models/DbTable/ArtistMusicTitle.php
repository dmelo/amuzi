<?php

class DbTable_ArtistMusicTitle extends DZend_Model_DbTable
{
    public function insert($data)
    {
        return $this->insertCachedWithoutException($data);
    }
}
