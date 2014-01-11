<?php

/**
 * DbTable_UserRow
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
class DbTable_UserRow extends DZend_Db_Table_Row
{
    public function save()
    {
        $this->setTable(new DbTable_User());
        parent::save();
    }

    public function getUrlToken()
    {
        return Zend_Registry::get('domain') .
            '/Auth/index/activate/email/' .
            urlencode($this->email) . '/token/' . $this->token;
    }

    public function getForgotPasswordUrl()
    {
        $config = new Zend_Config_Ini(
            APPLICATION_PATH . "/configs/application.ini", APPLICATION_ENV
        );
        $time = time(null);
        $hash = sha1($this->email . $time . $config->salt);

        return Zend_Registry::get('domain') .
            '/Auth/index/resetpassword/email/' . $this->email . '/time/' .
            $time . '/hash/' . $hash;
    }

    public function isForgotPasswordUrlValid($time, $hash)
    {
        $config = new Zend_Config_Ini(
            APPLICATION_PATH . "/configs/application.ini", APPLICATION_ENV
        );

        return (sha1($this->email . $time . $config->salt) === $hash);
    }

    public function postRegister()
    {
        $playlistDb = new DbTable_Playlist();
        $playlistSet = $playlistDb->findByUserId($this->id);
        if (0 === count($playlistSet)) {
            $playlistDb->insert(
                array(
                    'user_id' => $this->id,
                    'name' => $this->name,
                    'privacy' => $this->privacy
                )
            );
            $playlistRow = $playlistDb->findRowByUserId($this->id);
            $this->currentPlaylistId = $playlistRow->id;
            $this->save();
        }
    }

    /**
     * countPlaylists Count playlists owned/seen by the user.
     *
     * @return void
     */
    public function countPlaylists()
    {
        $playlistDb = new DbTable_Playlist();
        $playlistRowSet = $playlistDb->findByUserId($this->id);
        $count = 0;
        foreach ($playlistRowSet as $row) {
            $count++;
        }

        return $count;
    }

    public function getAction()
    {
        $action = '';
        if ('default' === $this->view) {
            $action = 'index';
        } elseif ('incboard' === $this->view) {
            $action = 'incboard';
        }

        return $action;
    }
}
