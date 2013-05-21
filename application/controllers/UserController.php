<?php

/**
 * UserController
 *
 * @package
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
class UserController extends DZend_Controller_Action
{
    public function init()
    {
        parent::init();
        if ('index' == $this->_request->getActionName())
            $this->_loginRequired = true;
    }

    public function indexAction()
    {
        $form = new Form_UserSettings();

        $params = $this->_request->getParams();
        if ($this->_request->isGet() && $form->isValid($params)) {
            $this->_userModel->setSettings($this->_request->getParams());
            $this->view->message = array('Saved', 'success');
        } else {
            $form->populate($this->_userModel->getSettings());
        }

        $this->view->form = $form;
    }

    public function getviewAction()
    {
        $this->view->view = $this->_userModel->getView();
    }

    public function pingAction()
    {
        $this->view->message = $this->_getUserId();
    }

    public function nextplaylistAction()
    {
        $userRow = $this->_getUserRow();
        $message = array();
        $next = false;
        $itemRow = null;
        $collectionRowSet = null;
        if (null !== $userRow->currentPlaylistId) {
            $collectionRowSet = $this->_playlistModel->fetchAllUsers();
            $itemId = $userRow->currentPlaylistId;
            $isAlbum = false;
        } elseif (null !== $userRow->currentAlbumId) {
            $collectionRowSet = $this->_albumModel->findAllFromUser();
            $itemId = $userRow->currentAlbumId;
            $isAlbum = true;
        }

        if (null !== $collectionRowSet) {
            foreach ($collectionRowSet as $p) {
                if (true === $next) {
                    $itemRow = $p;
                    break;
                } elseif ($itemId === $p->id) {
                    $next = true;
                }
            }
            if (null === $itemRow && true === $next) {
                foreach ($collectionRowSet as $p) {
                    $itemRow = $p;
                    break;
                }
            }

            if (null !== $itemRow) {
                $message = array($itemRow->id, $isAlbum);
            }
        } else {
            $message = array(
                'Could not determine next playlist/album', 'error'
            );
        }

        $this->view->message = $message;
    }
}
