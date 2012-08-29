<?php

class DbTable_MusicTitle extends DZend_Db_Table
{
    public function insert($data)
    {
        return $this->insertCachedWithoutException($data);
    }
}
