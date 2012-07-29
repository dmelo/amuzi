<?php

class DbTable_Artist extends DZend_Model_DbTable
{
    public function insert($data)
    {
        $key = 'artist_' . sha1(print_r($data, true));
        if (($ret = $this->_cache->load($key)) === false) {
            $ret = $this->insertWithoutException($data);
            $this->_cache->save($ret, $key);
        }

        return $ret;
    }
}
