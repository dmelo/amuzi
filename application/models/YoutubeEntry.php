<?php

class YoutubeEntry
{
    private $_fields;
    private $_data;

    public function __construct()
    {
        $this->_fields = array('id', 'title', 'content');
        $this->_data = array();
    }

    public function __construct($entry)
    {
        __construct();
        foreach($entry as $key => $value)
            if(in_array($key, $this->_fields))
                $this->_data[$key] = $value;
    }

    public function __get($key)
    {
        if(in_array($key, $this->_fields))
            return $this->_data[$key];
    }

    public function __set($key, $value)
    {
        if(in_array($key, $this->_fields))
            $this->_data[$key] = $value;
    }
}

