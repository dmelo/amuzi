<?php

class Bond extends DZend_Model
{
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
