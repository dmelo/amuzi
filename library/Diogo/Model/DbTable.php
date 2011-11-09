<?php

class Diogo_Model_DbTable extends Zend_Db_Table_Abstract
{
    /*
    protected $_adapter;

    public function __construct()
    {
        $this->_adapter = $this->getAdapter();
    }
    */

    public function findById($id)
    {
        $primary = $this->_primary;
        var_dump($primary);
        return null;
        //return $this->fetchRow($this->getAdapter()->quoteInto("${primary} = ?", $id));
    }
}
