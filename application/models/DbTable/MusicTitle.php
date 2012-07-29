<?php

class DbTable_MusicTitle extends DZend_Model_DbTable
{
    public function insert($data)
    {
        return $this->insertCachedWithoutException($data);
    }
}
