<?php

class Diogo_Model_DbTable extends Zend_Db_Table_Abstract
{
    public function findById($id)
    {
        $primary = $this->_primary;
        if(is_array($primary))
            $primary = reset($primary);
        return $this->fetchRow($this->getAdapter()->quoteInto("${primary} = ?", $id));
    }
}
