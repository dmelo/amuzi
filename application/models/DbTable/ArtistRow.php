<?php

/**
 * DbTable_ArtistRow
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
class DbTable_ArtistRow extends DZend_Db_Table_Row
{
    protected $_similarityList = null;
    protected $_topAlbumList = null;

    protected function _getDataFromLastfm()
    {
        $this->_logger->debug(
            'DbTable_ArtistRow::_getDataFromLastfm 01 ' . $this->id
        );
        $lastfmModel = new Lastfm();
        $ret = $lastfmModel->getArtist($this->name);
        $this->cover = $ret['cover'];
        $this->info = $ret['info'];
        $artistSimilarityModel = new ArtistSimilarity();
        $artistTopAlbumModel = new ArtistTopAlbum();
        $this->_logger->debug(
            'DbTable_ArtistRow::_getDataFromLastfm 02 ' . $this->id
        );
        $this->_similarityList = $artistSimilarityModel->insertSimilarities(
            $this->id, $ret['similarityList']
        );
        $this->_logger->debug(
            'DbTable_ArtistRow::_getDataFromLastfm 03 ' . $this->id
        );
        $this->_topAlbumList = $artistTopAlbumModel->getList($this->id);

        $this->save();
    }

    public function getCover()
    {
        if (null === $this->cover) {
            $this->_getDataFromLastfm();
        }

        return $this->cover;
    }

    public function getInfo()
    {
        if (null === $this->info) {
            $this->_getDataFromLastfm();
        }

        return $this->info;
    }

    public function getSimilarityList()
    {
        if (null === $this->_similarityList) {
            $this->_getDataFromLastfm();
        }

        return $this->_similarityList;
    }

    public function getTopAlbumList()
    {
        if (null === $this->_topAlbumList) {
            $this->_getDataFromLastfm();
        }

        return $this->_topAlbumList;
    }

    public function __get($name)
    {
        if ('similarityList' === $name) {
            return $this->_similarityList;
        } elseif ('pageUrl' === $name) {
            $domain = Zend_Registry::get('domain');
            return $domain . '/artist/' . urlencode($this->name);
        } else {
            return parent::__get($name);
        }
    }
}
