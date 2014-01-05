<?php

/**
 * AlbumController
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
class AlbumController extends DZend_Controller_Action
{
    protected function _fullTrackList(DbTable_AlbumRow $albumRow)
    {
        foreach ($albumRow->getTrackListSync() as $track) {
            if (2 === count($track)) {
                $this->_musicTrackLinkModel->getTrackSync(
                    $track['artist'], $track['musicTitle']
                );
            }
        }
    }

    public function init()
    {
        parent::init();
        $this->_jsonify = true;
        $this->_loginRequired = true;
        $this->_messageFail = array(
            $this->view->t('Failed saving setting'),
            'error'
        );

    }

    public function addAction()
    {
        if (($albumId = $this->_request->getParam('albumId')) !== false) {
            $this->_logModel->insert(
                $this->_request->getParam('windowId'),
                'add_album',
                $albumId,
                null,
                null,
                $this->_request->getParam('searchId'),
                $this->_request->getParam('rfDepth')
            );
            $this->view->output = $this->
                _userListenAlbumModel->insert($albumId);
            if (($albumRow = $this->_albumModel->findRowById($albumId))
                !== null) {
                $this->_fullTrackList($albumRow);
            }
        }
    }

    public function forcefullalbumAction()
    {
        $this->view->output = false;
        $filename = 'tmp/albumid.txt';
        $fd = fopen($filename, 'r');
        $id = 0;
        if (false === $fd) {
            $fd = fopen($filename, 'w');
            if ($fd === false) {
                $this->_logger->err(
                    'It wasn\'t possible to create file ' . $filename
                    . ', from album/forcealbum'
                );
                return;
            } else {
                fwrite($fd, '0');
                fclose($fd);
            }
        } else {
            $id = fscanf($fd, " %d ");
            fclose($fd);
        }

        $albumRow = $this->_albumModel->fetchNextAlbumRow($id);
        if (null !== $albumRow) {
            $this->_fullTrackList($albumRow);
            $fd = fopen($filename, 'w');
            fwrite($fd, $albumRow->id);
            fclose($fd);
            $this->view->output = $albumRow->id;
        }
    }

    public function loadAction()
    {
        if (($id = $this->_request->getParam('id')) !== null
            || ($id = $this->_getUserRow()->currentAlbumId) !== null) {
            $albumRow = $this->_albumModel->findRowById($id);
            $userRow = $this->_getUserRow();
            if (null !== $userRow) {
                $userRow->currentAlbumId = $albumRow->id;
                $userRow->save();
            }

            $this->view->output = $albumRow->export();
        }
    }

    public function infoAction()
    {
        if (($id = $this->_request->getParam('id')) !== null ||
            (
                ($name = $this->_request->getParam('name')) !== null &&
                ($artist = $this->_request->getParam('artist')) !== null )
            ) {
            $collection = null;
            if (null !== $id) {
                $this->_logger->debug("AlbumController::info $id");
                $collection = $this->_albumModel->findRowById($id);
            } else {
                $this->_logger->debug("AlbumController::info $artist - $name");
                $collection = $this->_albumModel->fetch($artist, $name);
            }

            if (null !== $collection) {
                $this->view->collection = $collection;
                $c = new DZend_Chronometer();

                $collection->getCoverName();
                $this->view->artistRow = $this->_artistModel->findRowById(
                    $collection->artistId
                );

                foreach (array(
                    'getCoverName', 'getType', 'playTime', 'getTrackListSync'
                    ) as $f) {
                    $c->start();
                    $collection->$f();
                    $c->stop();
                    $this->_logger->debug(
                        "AlbumController::info $f " . $c->get()
                    );
                }

                $this->renderScript('playlist/info.phtml');
            } else {
            }
        }
    }

    public function listAction()
    {
        $this->view->playlistRowSet = $this->_albumModel->findAllFromUser();
        $artistIds = array();
        foreach ($this->view->playlistRowSet as $row) {
            $artistIds[] = $row->artistId;
        }
        $this->_artistModel->preload($artistIds);
        $this->renderScript('playlist/list.phtml');
    }

    public function removeAction()
    {
        $message = array();

        if ($this->_request->isPost() &&
            ($id = $this->_request->getPost('id')) !== null) {
            if (($msg = $this->_albumModel = $this->_albumModel->remove($id))
                === true) {
                $message = array($this->view->t('Album removed'), 'success');
            } else {
                $message = array(
                    $this->view->t('Could not remove') . ': ' . $msg, 'error'
                );
            }
        }

        $this->view->output = $message;
    }

    protected function _adjustSetting($setting, $id, $value)
    {
        $message = $this->_messageFail;

        if (is_string($setting) && null !== $id && null !== $value) {
            try {
                $row = $this->_userListenAlbumModel->findByAlbumId(
                    $id
                );
                $row->$setting = $value;
                $row->save();
                $message = array($this->view->t('Setting saved'), true);
            } catch (Zend_Exception $e) {
                $message = $this->_messageFail;
                $this->_logger->debug(
                    $e->getMessage() . ' # ' . $e->getStackAsString()
                );
            }
        }

        return $message;
    }

    public function setrepeatAction()
    {
        $this->view->output = $this->_adjustSetting(
            'repeat',
            $this->_request->getPost('id'),
            $this->_request->getPost('repeat')
        );
    }

    public function setshuffleAction()
    {
        $this->view->output = $this->_adjustSetting(
            'shuffle',
            $this->_request->getPost('id'),
            $this->_request->getPost('shuffle')
        );
    }
}
