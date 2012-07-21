<?php

class DbTable_MusicTitle extends DZend_Model_DbTable
{
    public function insert($data)
    {
        try {
            return parent::insert($data);
        } catch(Zend_Db_Exception $e) {
            $row = $this->findRowByName($data['name']);
            return $row->id;
        }
    }
}
