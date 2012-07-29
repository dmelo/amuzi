<?php

class DbTable_MusicSimilarity extends DZend_Model_DbTable
{
    public function insert($data)
    {
        return $this->insertCachedWithoutException($data);
    }
}
