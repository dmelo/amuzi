<?php

class DbTable_UserRow extends DZend_Model_DbTableRow
{
    public function save()
    {
        $this->setTable(new DbTable_User());
        parent::save();
    }
}
