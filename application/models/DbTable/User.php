<?php

class DbTable_User extends Diogo_Model_DbTable
{
    protected $_name = 'user';
    protected $_rowClass = 'DbTable_UserRow';
    protected $_primary = 'id';

    public function findByFacebookId($facebookId)
    {
        $where = $this->_adapter->quoteInto('facebookId = ?', $facebookId);
        return $this->fetchRow($where);
    }

    /**
     * register Make sure the user is registered on the database.
     *
     * @param mixed $data Array that contains information about the user.
     * @return Zend_Db_Table_Row returns the user row.
     */
    public function register($data)
    {
        $ret = null;
        try {
            // Register the user.
            $id = $this->insert($data);
            $ret = $this->findById($id);
        } catch(Zend_Db_Statement_Exception $e) {
            // In case the user is already registered, just update his info.

            $where = $this->getAdapter()->quoteInto('facebookId = ?', $data['facebookId']);
            $this->update($data, $where);
            $ret = $this->findByFacebookId($data['facebookId']);
        }

        return $ret;
    }
}

