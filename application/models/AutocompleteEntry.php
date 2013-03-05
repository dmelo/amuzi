<?php

/**
 * AutocompleteEntry
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
class AutocompleteEntry extends AbstractEntry
{
    public function __construct($artist, $musicTitle, $cover, $type)
    {
        $this->_fields = array(
            'name',
            'cover',
            'artist',
            'musicTitle',
            'type'
        );

        foreach ($this->_fields as $field) {
            if ('name' !== $field) {
                $this->$field = $$field;
            }
        }
    }

    public function __get($key)
    {
        if ('name' === $key) {
            return "{$this->artist} - {$this->musicTitle}";
        } elseif ('cover' === $key) {
            $ret = '/img/album.png';
            if (null !== $this->_data['cover']) {
                $ret = $this->_data['cover'];
            }

            return $ret;
        } else {
            return parent::__get($key);
        }
    }

    public function getArray()
    {
        $ret = array();
        foreach ($this->_fields as $field) {
            $ret[$field] = $this->$field;
        }

        return $ret;
    }

    public function resultLength()
    {
        return strlen($this->name);
    }
}
