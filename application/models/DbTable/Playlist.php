<?php

/**
 * DbTable_Playlist
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
class DbTable_Playlist extends DZend_Db_Table
{
    public function insert(array $data)
    {
        // All checks
        $firstLetter = ucfirst(substr($data['name'], 0, 1));
        if($firstLetter < 'A' || $firstLetter > 'Z')
            throw new Zend_Db_Table_Exception(
                "playlist.name must start with a letter"
            );

        return parent::insert($data);
    }

    public function findByName($name)
    {
        $user = $this->_session->user;
        return $this->findRowByUserIdAndName($user->id, $name);
    }

    public function search($q, $limit = 10, $offset = 0)
    {
        $where = $this->_db->quoteInto('name like ?', '%' . $q . '%');
        $where .= $this->_db->quoteInto(
            ' AND user_id = ?',
            $this->_session->user->id
        );
        return $this->fetchAll(
            $this->select()
            ->where($where)->order('name')->limit($limit, $offset)
        );
    }

    public function fetchAllUsers()
    {
        $select = $this->select()
            ->where(
                $this->_db->quoteInto('user_id = ?', $this->_session->user->id)
            )
            ->order('name');
        return $this->fetchAll($select);
    }

    public function create($userId, $name, $public = 'public')
    {
        $data = array(
            'user_id' => $userId,
            'name' => $name,
            'privacy' => $public ? 'public' : 'private'
            );
        return $this->findRowById($this->insert($data));
    }
}
