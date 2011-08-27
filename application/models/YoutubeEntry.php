<?php

class YoutubeEntry
{
    private $_fields;
    private $_data;

    public function __construct($entry = array( ) )
    {
        $this->_fields = array('id', 'title', 'content');
        $this->_data = array();

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
        if (in_array($key, $this->_fields)) {
            if ( 'id' === $key )
                $value = str_replace('http://gdata.youtube.com/feeds/api/videos/' , '', $value);
            $this->_data[$key] = $value;
        }
    }
}

