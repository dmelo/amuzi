<?php

class DbTable_ArtistMusicTitle extends DZend_Db_Table
{
    public function insert($data)
    {
        return $this->insertCachedWithoutException($data);
    }
}
