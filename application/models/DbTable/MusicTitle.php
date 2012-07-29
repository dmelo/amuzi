<?php

class DbTable_MusicTitle extends DZend_Model_DbTable
{
    protected $_cache;

    public function insert($data)
    {
        $key = 'music_title_' . sha1(print_r($data, true));
        if (($ret = $this->_cache->load($key)) === false) {
            $ret = $this->insertWithoutException($data);
            $this->_cache->save($ret, $key);
        }

        return $ret;
    }
}
