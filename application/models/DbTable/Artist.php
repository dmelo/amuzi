<?php

/**
 * DbTable_Artist
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
class DbTable_Artist extends DZend_Db_Table
{
    public function insert($data)
    {
        if (array_key_exists('name', $data)) {
            $data['name'] = substr($data['name'], 0, 62);
        }

        if (($id = $this->_hscache->load(md5($data['name']))) === false) {
            $id = $this->insertCachedWithoutException($data);
            $this->_hscache->save($id, md5($data['name']));
        }

        return $id;
    }

    public function findRowById($id)
    {
        try {
            $artistList = Zend_Registry::get('artistList');
        } catch (Zend_Exception $e) {
            $artistList = array();
        }
        $ret = null;
        if (array_key_exists($id, $artistList)) {
            $ret = $artistList[$id];
        } else {
            $ret = parent::findRowById($id);
        }

        return $ret;
    }

    public function preload($ids)
    {
        try {
            $artistList = Zend_Registry::get('artistList');
        } catch (Zend_Exception $e) {
            $artistList = array();
        }
        $l = $this->findById($ids);
        foreach ($l as $row) {
            $artistList[$row->id] = $row;
        }

        Zend_Registry::set('artistList', $artistList);
    }
}
