<?php

class Artist
{
    protected $_artistDb;

    public function __construct()
    {
        $this->_artistDb = new DbTable_Artist();
    }

    public function insert($name)
    {
        return $this->_artistDb->insert(array('name' => $name));
    }
}
