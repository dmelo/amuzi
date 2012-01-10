<?php

class DbTable_UserRow extends Zend_Db_Table_Row_Abstract
{
    public function save()
    {
        $this->setTable(new DbTable_User());
        parent::save();
    }
}
