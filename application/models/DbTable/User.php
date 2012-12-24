<?php

/**
 * DbTable_User
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
class DbTable_User extends DZend_Db_Table
{
    /**
     * register Make sure the user is registered on the database.
     *
     * @param mixed $data Array that contains information about the user.
     * @return Zend_Db_Table_Row returns the user row.
     */
    public function register($data)
    {
        $ret = null;
        try {
            // Register the user.
            $id = $this->insert($data);
            $ret = $this->findRowById($id);
            $playlistDb = new DbTable_Playlist();
            $playlistRow = $playlistDb->create($ret->id, 'default');
            $ret->currentPlaylistId = $playlistRow->id;
            $ret->save();
        } catch(Zend_Db_Statement_Exception $e) {
            // In case the user is already registered, just update his info.

            $where = $this->_db->quoteInto(
                'facebook_id = ?', $data['facebook_id']
            );
            $this->update($data, $where);
            $ret = $this->findRowByFacebookId($data['facebook_id']);
        }

        return $ret;
    }

    public function findCurrent()
    {
        return $this->_session->user;
    }
}
