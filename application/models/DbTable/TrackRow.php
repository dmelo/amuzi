<?php

class DbTable_TrackRow extends DZend_Model_DbTableRow
{
    public function getArray()
    {
        $columns = array(
            'id',
            'title',
            'fid',
            'fcode',
            'url',
            'cover',
            'duration',
            'youtubeUrl'
        );
        $ret = array();
        foreach ($columns as $column)
            $ret[$column] = $this->$column;
        return $ret;
    }

    public function __get($name)
    {
        if ('url' === $name) {
            // For now it's prepared for youtube only.
            return Zend_Registry::get('domain') . '/api/' . $this->duration
                . '/' . $this->fid . '/' . urlencode($this->title) . '.mp3';
        } elseif ('youtubeUrl' === $name)
            return 'http://www.youtube.com/watch?v=' . $this->fid;
        else
            return parent::__get($name);
    }

}
