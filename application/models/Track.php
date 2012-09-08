<?php

/**
 * Track
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
class Track extends DZend_Model
{
    /**
     * findRowById Find a row, given it's ID.
     *
     * @param int $id Row ID
     * @return Returns the asked row.
     */
    public function findRowById($id)
    {
        return $this->_trackDb->findRowById($id);
    }

    /**
     * insert Insert a row.
     *
     * @param mixed $data
     * @return DbTable_TrackRow Returns the inserted row.
     */
    public function insert($data)
    {
        return $this->_trackDb->insert($data);
    }

}
