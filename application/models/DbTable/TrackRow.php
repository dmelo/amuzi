<?php

class DbTable_TrackRow extends DZend_Model_DbTableRow
{
    public function getArray()
    {
        $columns = array('title', 'url', 'cover', 'duration');
        $ret = array();
        foreach ($columns as $column)
            $ret[$column] = $this->$column;
        return $ret;
    }
}
