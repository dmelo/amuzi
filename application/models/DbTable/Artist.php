<?php

class DbTable_Artist extends DZend_Model_DbTable
{
    public function insert($data)
    {
        return $this->insertWithoutException($data);
    }
}
