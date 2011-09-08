<?php

/**
 * YoutubeEntry
 *
 * @package You2Better
 * @version 1.0
 * @copyright Copyright (C) 2010 Diogo Oliveira de Melo. All rights reserved.
 * @author Diogo Oliveira de Melo <dmelo87@gmail.com>
 * @license GPL version 3
 */
class YoutubeEntry
{
    private $_fields;
    private $_data;

    /**
     * __construct
     *
     * @param array $entry
     * @return void
     */
    public function __construct($entry = array( ) )
    {
        $this->_fields = array('id', 'title', 'content', 'you2better', 'pic');
        $this->_data = array();

        foreach($entry as $key => $value)
            if(in_array($key, $this->_fields))
                $this->_data[$key] = $value;
    }

    /**
     * __get
     *
     * @param mixed $key
     * @return void
     */
    public function __get($key)
    {
        if(in_array($key, $this->_fields))
            return $this->_data[$key];
    }

    /**
     * __set
     *
     * @param mixed $key
     * @param mixed $value
     * @return void
     */
    public function __set($key, $value)
    {
        if (in_array($key, $this->_fields)) {
            if ( 'id' === $key )
                $value = str_replace(
                    'http://gdata.youtube.com/feeds/api/videos/' , '', $value);
            $this->_data[$key] = $value;
        }
    }

    /**
     * getArray
     *
     * @return void
     */
    public function getArray()
    {
        $item = array();
        $item['id'] = $this->id;
        $item['title'] = $this->title;
        $item['content'] = $this->content;
        $item['you2better'] = $this->you2better;
        $item['pic'] = $this->pic;

        return $item;
    }
}
