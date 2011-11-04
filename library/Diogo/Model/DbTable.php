<?php

class Diogo_Model_DbTable extends Zend_Db_Table_Abstract
{
    protected $_primary = 'id';
    /*
    protected $_adapter;

    public function __construct()
    {
        $this->_adapter = $this->getAdapter();
    }*/

    public function findById($id)
    {
        return $this->fetchRow($this->getAdapter()->quoteInto("$this->_primary = ?", $id));
    }
}
