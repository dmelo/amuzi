<?php

class Bond extends DZend_Model
{
    public function __get($name)
    {
        if(preg_match('/^_.*Model$/', $name) || preg_match('/^_.*Db$/', $name))
            return parent::__get($name);
        elseif(($row = $this->_bondDb->findRowByName($name)) !== null)
            return $name;
        else
            trigger_error("Undefined property ${name}", E_USER_NOTICE);

        return null;
    }

    public function findRowById($id)
    {
        return $this->_bondDb->findRowById($id);
    }

    public function findRowByName($name)
    {
        return $this->_bondDb->findRowByName($name);
    }
}
