<?php

/**
 * YoutubeEntry
 *
 * @package Amuzi
 * @version 1.0
 * Amuzi - Online music
 * Copyright (C) 2010-2014  Diogo Oliveira de Melo
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
/**
 * YoutubeEntry
 *
 * @package You2Better
 * @version 1.0
 * @copyright Copyright (C) 2010 Diogo Oliveira de Melo. All rights reserved.
 * @author Diogo Oliveira de Melo <dmelo87@gmail.com>
 * @license GPL version 3
 */
class YoutubeEntry extends AbstractEntry
{
    public $fcode;
    /**
     * __construct
     *
     * @param array $entry
     * @return void
     */
    public function __construct($entry = array( ) )
    {
        $this->fcode = 'y';
        $this->_fields = array(
            'id', 'title', 'fid', 'cover', 'duration', 'artist', 'musicTitle',
            'artistMusicTitleId'
        );
        $this->_data = array();

        foreach($entry as $key => $value)
            if(in_array($key, $this->_fields))
                $this->_data[$key] = $value;
    }

    /**
     * __set Set attribute value.
     *
     * @param mixed $key
     * @param mixed $value
     * @return void
     */
    public function __set($key, $value)
    {
        parent::__set($key, $value);
        if ('id' === $key)
            $value = str_replace(
                'http://gdata.youtube.com/feeds/api/videos/', '', $value
            );
        $this->_data[$key] = $value;
    }


    /**
     * getArray Get the youtube entry on array format.
     *
     * @return void
     */
    public function getArray()
    {
        $item = array();
        foreach($this->_fields as $field)
            $item[$field] = null !== $this->$field ? $this->$field : '';

        return $item;
    }
}
