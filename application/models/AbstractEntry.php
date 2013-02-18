<?php

/**
 * AbstractEntry
 *
 * @package Amuzi
 * @version 1.0
 * Amuzi - Online music
 * Copyright (C) 2010-2013  Diogo Oliveira de Melo
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
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
        if(in_array($key, $this->_fields) && array_key_exists($key, $this->_data))
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

    public function __toString()
    {
        $ret = '';
        foreach ($this->_fields as $field) {
            $ret .= $field . ': ' . (is_array($this->$field) ? print_r($this->$field, true) : $this->$field) . PHP_EOL;
        }

        return $ret;
    }

    /**
     * getArray
     *
     * @return void
     */
    public function getArray()
    {
        return $this->_data;
    }
}
