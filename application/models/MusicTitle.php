<?php

class MusicTitle extends DZend_Model
{
    public function insert($name)
    {
        return $this->_musicTitleDb->insert(array('name' => $name));
    }
}
