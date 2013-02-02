<?php

/**
 * UserListenAlbum
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
class UserListenAlbum extends DZend_Model
{
    public function insert($albumId)
    {
        $ret = null;
        if (($albumRow = $this->_albumDb->findRowById($albumId)) !== null) {
            try {
            $ret = $this->_userListenAlbumDb->insert(
                array(
                    'album_id' => $albumRow->id,
                    'user_id' => $this->_session->user->id
                )
            );
            } catch (Zend_Db_Statement_Exception $e) {
                $row = $this->_userListenAlbumDb->findRowByAlbumIdAndUserId(
                    $albumRow->id,
                    $this->_session->user->id
                );

                $ret = null !== $row ? $row->id : null;
            }
        }

        return $ret;
    }
}
