<?php

class Bond
{
    protected $_bondDb;

    public function __construct()
    {
        $this->_bondDb = new DbTable_Bond();
    }

    public function findRowByName($name)
    {
        return $this->_bondDb->findRowByName($name);
    }

    public function findRowById($id)
    {
        return $this->_bondDb->findRowById($id);
    }

    public function __get($name)
    {
        $row = $this->findRowByName($name);
        if(null !== $row)
            return $name;
        else
            trigger_error("Undefined property ${name}", E_USER_NOTICE);

        return null;
    }
}
