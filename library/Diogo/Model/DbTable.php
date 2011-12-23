<?php

class Diogo_Model_DbTable extends Zend_Db_Table_Abstract
{
    protected $_db;
    protected $_session;

    public function __construct($config = array())
    {
        parent::__construct($config);
        $this->_db = $this->getAdapter();
        $this->_session = new Zend_Session_Namespace('session');
    }

    public function findById($id)
    {
        $primary = $this->_primary;
        if(is_array($primary))
            $primary = reset($primary);
        return $this->fetchRow($this->getAdapter()->quoteInto("${primary} = ?", $id));
    }
}
