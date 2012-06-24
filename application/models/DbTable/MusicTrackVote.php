<?php

class DbTable_MusicTrackVote extends DZend_Model_DbTable
{
    public function insert($data)
    {
        return $this->insertWithoutException($data);
    }
}
