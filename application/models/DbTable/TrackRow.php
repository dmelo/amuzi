<?php

/**
 * DbTable_TrackRow
 *
 * @package Amuzi
 * @version 1.0
 * Amuzi - Online music
 * Copyright (C) 2010-2012  Diogo Oliveira de Melo
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
class DbTable_TrackRow extends DZend_Db_Table_Row
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
            return 'http://www.youtube.com/embed/' . $this->fid . '?autoplay=1&rel=0';
        else
            return parent::__get($name);
    }

}
