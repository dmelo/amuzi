<?php

/**
 * DbTable_Artist
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
class DbTable_Artist extends DZend_Db_Table
{
    protected $_allowRequestCache = true;

    public function insert(array $data)
    {
        if (array_key_exists('name', $data)) {
            $data['name'] = substr($data['name'], 0, 62);
        }

        try {
            $id = $this->insertCachedWithoutException($data);
        } catch (Zend_Db_Statement_Exception $e) {
            $row = $this->findRowByName($data['name']);
            if (null !== $row) {
                $row->cover = $data['cover'];
                $row->save();
                $id = $row->id;
            } else {
                throw $e;
            }
        }

        return $id;
    }
}
