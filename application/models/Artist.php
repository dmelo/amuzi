<?php

/**
 * Artist
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
class Artist extends DZend_Model
{
    public function insert($name, $cover = null)
    {
        $row = null;
        try {
            $id = $this->_objDb->insert(
                array(
                    'name' => $name,
                    'cover' => $cover
                )
            );
            $row = $this->findRowById($id);
        } catch (Zend_Db_Statement_Exception $e) {
            $row = $this->findRowByName($name);
        }

        if (null == $row->cover) {
            $row->cover = $cover;
            $row->save();
        }

        return $row->id;
    }

    public function preload($ids)
    {
        $this->_objDb->preload($ids);
    }

    public function findRowById($id)
    {
        return $this->_objDb->findRowById($id);
    }

    public function findRowByName($name)
    {
        return $this->_objDb->findRowByName($name);
    }

    public function findById($idSet)
    {
        return $this->_objDb->findById($idSet);
    }
}
