<?php

/**
 * LastfmAlbum
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
class LastfmAlbum extends AbstractEntry
{
    public function __construct($name, $cover, $artist, $trackList)
    {
        $this->_fields = array(
            'name',
            'cover',
            'artist',
            'trackList'
        );

        foreach ($this->_fields as $field) {
            if (null !== $$field) {
                $this->_data[$field] = $$field;
            }
        }
    }

    public function getArray()
    {
        $ret = array();
        foreach ($this->_fields as $field) {
            if (is_array($this->$field)) {
                $ret[$field] = array();
                foreach ($this->$field as $item) {
                    $ret[$field][] = $item->getArray();
                }
            } else {
                $ret[$field] = $this->$field;
            }
        }

        return $ret;
    }
}
