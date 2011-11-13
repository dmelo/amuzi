<?php

class DbTable_Track extends Diogo_Model_DbTable
{
    protected $_name = 'track';
    protected $_primary = 'id';
    protected $_rowClass = 'DbTable_TrackRow';

    public function findByUrl($url)
    {
        $where = $this->getAdapter()->quoteInto('url = ?', $url);
        return $this->fetchRow($where);
    }

    public function insert($data)
    {
        $trackRow = $this->findByUrl($data['url']);
        if(!$trackRow) {
            parent::insert($data);
            $trackRow = $this->findByUrl($data['url']);
        }

        return $trackRow;
    }
}

