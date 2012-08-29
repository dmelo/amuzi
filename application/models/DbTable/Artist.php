<?php

class DbTable_Artist extends DZend_Db_Table
{
    public function insert($data)
    {
        $data['name'] = substr($data['name'], 0, 62);
        return $this->insertCachedWithoutException($data);
    }
}
