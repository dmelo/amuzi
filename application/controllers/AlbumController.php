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
    public function init()
    {
        parent::init();
        $this->_jsonify = true;
    }

    public function addAction()
    {
        if (($albumId = $this->_request->getParam('albumId')) !== false) {
            $this->view->output = $this->
                _userListenAlbumModel->insert($albumId);
            $albumRow = $this->_albumModel->findRowById($albumId);
            $albumRow->getTrackListSync();
            foreach ($albumRow->trackList as $track) {
                if (2 === count($track)) {
                    $artist = $track['artist'];
                    $musicTitle = $track['musicTitle'];
                    $this->_musicTrackLinkModel->getTrackSync(
                        $artist, $musicTitle
                    );
                    // I don't have to store the value because it doesn't
                    // return the album.
                }
            }
        }
    }

    public function loadAction()
    {
        if (($id = $this->_request->getParam('id')) !== null
            || ($id = $this->_session->user->currentAlbumId) !== null) {
            $albumRow = $this->_albumModel->findRowById($id);
            $this->_session->user->currentAlbumId = $albumRow->id;
            $this->_session->user->currentPlaylistId = null;
            $this->_session->user->save();

            $album = $albumRow->getArray();
            $ret = array($album['trackList'], $album['name'], 0, 0, 0);
            $this->view->output = $ret;
        }
    }

    public function infoAction()
    {
        if (($id = $this->_request->getParam('id')) !== false) {
            $collection = $this->view->collection = $this->_albumModel
                ->findRowById($id);
            $c = new DZend_Chronometer();

            $collection->getCoverName();

            foreach (array(
                'getCoverName', 'getType', 'playTime', 'getTrackListAsArray'
                ) as $f) {
                $c->start();
                $collection->$f();
                $c->stop();
                $this->_logger->debug("AlbumController::info $f " . $c->get());
            }


            $this->renderScript('playlist/info.phtml');
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
}
