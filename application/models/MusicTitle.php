<?php

class MusicTitle
{
    protected $_musicTitleDb;

    public function __construct()
    {
        $this->_musicTitleDb = new DbTable_MusicTitle();
    }

    public function insert($name)
    {
        return $this->_musicTitleDb->insert(array('name' => $name));
    }
}
