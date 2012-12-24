<?php

/**
 * DbTable_TrackRow
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
            'youtubeUrl',
            'youtubeUrlEmbedded',
            'facebookUrl',
            'shareUrl'
        );
        $ret = array();
        foreach ($columns as $column)
            $ret[$column] = $this->$column;
        return $ret;
    }

    public function __get($name)
    {
        $domain = Zend_Registry::get('domain');
        $extensionList = array('mp3', 'flv');
        if (in_array($name, $extensionList) || 'url' === $name) {
            $url = $domain . '/api/' . $this->duration
                . '/' . $this->fid . '/' . urlencode($this->title);
        }

        if ('url' === $name) {
            return $url . '.flv';
        } elseif (in_array($name, $extensionList)) {
            return $url . '.' . $name;
        } elseif ('youtubeUrl' === $name) {
            return 'http://www.youtube.com/watch?v=' . $this->fid;
        } elseif ('youtubeUrlEmbedded' === $name) {
            return 'http://www.youtube.com/embed/' . $this->fid
                . '?autoplay=1&rel=0';
        } elseif ('facebookUrl' === $name) {
            return 'http://facebook.com/share.php?u=' . urlencode($this->shareUrl);
        } elseif ('shareUrl' === $name || 'share_url' === $name) {
            return $domain . '/share/index/command/t/param/' . $this->id;
        } else {
            return parent::__get($name);
        }
    }
}
