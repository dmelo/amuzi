<?php

class Artist extends DZend_Model
{
    public function insert($name)
    {
        return $this->_artistDb->insert(array('name' => $name));
    }
}
