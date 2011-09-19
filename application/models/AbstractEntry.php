<?php

abstract class AbstractEntry
{
    protected $_fields;
    protected $_data;

    /**
     * __construct Setting up initial values.
     *
     * @param array $entry
     * @return void
     */
    public function __construct($entry = array( ) )
    {
        $this->_fields = array();
        $this->_data = array();
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
        if (in_array($key, $this->_fields))
            $this->_data[$key] = $value;
    }
}
