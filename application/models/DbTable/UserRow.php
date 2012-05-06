<?php

class DbTable_UserRow extends DZend_Model_DbTableRow
{
    public function save()
    {
        $this->setTable(new DbTable_User());
        parent::save();
    }

    public function getUrlToken()
    {
        return Zend_Registry::get('domain') . '/Auth/index/activate/email/' . urlencode($this->email) . '/token/' . $this->token;
    }
}
