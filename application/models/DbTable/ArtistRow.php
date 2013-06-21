<?php

/**
 * DbTable_ArtistRow
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
class DbTable_ArtistRow extends DZend_Db_Table_Row
{
    protected $_similarityList;

    protected function _getDataFromLastfm()
    {
        $lastfmModel = new Lastfm();
        $ret = $lastfmModel->getArtist($this->name);
        $this->cover = $ret['cover'];
        $this->info = $ret['info'];
        $this->_similarityList = $ret['similarityList'];

        $this->save();
    }

    public function getCover()
    {
        // if (null === $this->cover) {
            $this->_getDataFromLastfm();
        // }

        return $this->cover;
    }

    public function getInfo()
    {
        // if (null === $this->info) {
            $this->_getDataFromLastfm();
        // }

        return $this->info;
    }

    public function __get($name)
    {
        if ('similarityList' === $name) {
            return $this->_similarityList;
        } else {
            return parent::__get($name);
        }
    }
}
