<?php

class DbTable_User extends Zend_Db_Table_Abstract
{
    protected $_name = 'user';
    protected $_rowClass = 'DbTable_UserRow';

    public function register($data)
    {
        try {
            // Register the user.
            $this->insert($data);
        } catch(Zend_Db_Statement_Exception $e) {
            // In case the user is already registered, just update his info.

            $where = $this->getAdapter()->quoteInto('facebookId = ?', $data['facebookId']);
            unset($data['facebookId']);
            $this->update($data, $where);
        }
    }
}

