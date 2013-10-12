<?php

/**
 * PlaylistController Actions regarding playlist management and viewing.
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
class PlaylistController extends DZend_Controller_Action
{
    protected $_messageFail;

    public function init()
    {
        parent::init();
        $this->_logger->debug("debug....");

        $this->_loginRequired = true;
        $this->_messageFail = array(
            $this->view->t('Failed saving setting'),
            'error'
        );
    }

    public function indexAction()
    {
        $this->view->resultSet = $this->_playlistModel->search('');
        $this->view->form = new Form_PlaylistSettings();
        $this->view->formEdit = new Form_PlaylistEditName();
    }

    public function searchAction()
    {
        $this->view->formEdit = new Form_PlaylistEditName();
        if ($this->_request->isPost()) {
            $this->view->resultSet = $this->_playlistModel->search(
                $this->_request->getPost('q')
            );
        }
        else
            $this->getResponse()->setHttpResponseCode(500);
    }

    /**
     * saveAction Save the playlist and link it to the user currently logged
     * in.
     *
     * @return void
     */
    public function saveAction()
    {
        if ($this->_request->isPost()) {
            $playlist = $this->_request->getPost('playlist');
            $trackSet = array();

            for ($i = 0; $i < count($playlist); $i++) {
                $trackSet[$i]['id'] = $playlist[$i]['id'];
            }

            $p = DZend_Session_Namespace::get('session')->playlist = array(
                $trackSet,
                $this->_request->getPost('name')
            );
            $userRow = DZend_Session_Namespace::get('session')->user;
            DZend_Session_Namespace::close();

            if (null !== $userRow) {
                $this->_playlistModel->import(
                    $p[0],
                    $p[1]
                );
            }
        }
    }

    /**
     * addtrackAction Receives a post request informing ( title, url and
     * cover ) or ( id ) of the track to be added into the playlist, the
     * playlist's name is also passed by post parameter. As a reponse, this
     * action will send (in Json) an string, a boolean value indicating whether
     * the it was succeded or not and the track itself (with it's ID).
     *
     * @return void
     */
    public function addtrackAction()
    {
        $message = null;

        if ($this->_request->isPost()) {
            if ($this->_request->getPost('id') !== null) {
                $trackInfo = array('id' => $this->_request->getPost('id'));
            } else {
                $trackInfo = array('title' => $this->_request->getPost('title'),
                    'url' => $this->_request->getPost('url'),
                    'cover' => $this->_request->getPost('cover'));
            }

            if (
                ($artist = $this->_request->getPost('artist')) !== null &&
                ($musicTitle = $this->_request->getPost('musicTitle')) !== null
            ) {
                $artistMusicTitleId = $this->_artistMusicTitleModel
                    ->insert($artist, $musicTitle);
                $trackInfo['artist_music_title_id'] = $artistMusicTitleId;
            }


            $this->_logger->debug(
                'PlaylistController::addtrackAction -- ' .
                print_r($trackInfo, true)
            );

            try {
                $playlistRow = null;
                if (($isAlbum = $this->_request->getPost('isAlbum', false))
                    == true) {
                    $playlistRow = $this->_playlistModel->getCurrentRow();
                } else {
                    $playlistRow = $this->_playlistModel->create(
                        $this->_request->getPost('playlist')
                    );
                }

                $this->_logger->debug(
                    'PlaylistController::addtrack ' . $playlistRow->id
                    . '#' . $playlistRow->name
                );

                $trackRow = $playlistRow->addTrack($trackInfo);
                $this->_logModel->insert(
                    $this->_request->getParam('windowId'),
                    'add_track',
                    null,
                    $trackRow->id,
                    null
                );

                if (isset($artistMusicTitleId)) {
                    $this->_musicTrackLinkModel->bond(
                        $artistMusicTitleId,
                        $trackRow->id,
                        $this->_bondModel->insert_playlist
                    );
                }

                $trackArray = $trackRow->getArray();
                if (isset($artistMusicTitleId)) {
                    $trackArray['artistMusicTitleId'] = $artistMusicTitleId;
                }

                $message = array(
                    $this->view->t(
                        'Track added into playlist ' . $playlistRow->name
                    ),
                    'success',
                    $trackArray
                );
            } catch(Zend_Exception $e) {
                $message = array(
                    $this->view->t('Problems adding the track') . ': ' .
                        $e->getMessage(),
                    'error'
                );
            }
        } else {
            $message = array(
                $this->view->t('Problems adding track') . ': ' .
                $this->view->t('Invalid request'),
                'error'
            );
        }

        $this->view->message = $message;
    }

    public function rmtrackAction()
    {
        $message = null;

        if ($this->_request->isPost()) {
            $trackId = $this->_request->getPost('trackId');
            $playlist = $this->_request->getPost('playlist');
            try {
                $this->_playlistModel->rmTrack($trackId, $playlist);
                $message = array($this->view->t('Track removed'), 'success');
            } catch(Zend_Exception $e) {
                $message = array(
                    $this->view->t('Problems removing the track: ') +
                        $e->getMessage(),
                    'error'
                 );
            }

        }

        $this->view->message = $message;
    }

    /**
     * loadAction Loads the user's playlist.
     *
     * @return void
     */
    public function loadAction()
    {
        if ($this->_request->isPost()) {
            $session = DZend_Session_Namespace::get('session');
            $userRow = $session->user;
            $playlistRow = isset($session->playlist) ?
                $session->playlist : null;
            DZend_Session_Namespace::close();
            unset($session);
            if (isset($userRow)) {
                $id = $this->_request->getPost('id');

                if (null == $id &&
                    null !== $userRow->currentAlbumId) {
                    $this->_helper->redirector('load', 'album');
                } else {
                    $playlist = $this->_playlistModel->export($id);
                    $this->view->playlist = $playlist;
                    $userRow->currentPlaylistId = $id;
                    $userRow->save();
                }
            } else {
                $this->view->playlist = null;
            }
        }

        $this->_logger->debug(print_r($this->view->playlist, true));
    }

    public function infoAction()
    {
        $session = DZend_Session_Namespace::get('session');
        $userRow = $session->user;
        DZend_Session_Namespace::close();
        if (($id = $this->_request->getParam('id')) !== false
            && null !== $userRow) {
                $this->view->collection = $this->_playlistModel
                    ->findRowById($id);
        }
    }

    public function setrepeatAction()
    {
        $message = null;
        if ($this->_request->isPost()) {
            $repeat = $this->_playlistModel->setRepeat(
                $this->_request->getPost('name'),
                $this->_request->getPost('repeat')
            );
            if ($repeat)
                $message = array($this->view->t('Setting saved'), true);
            else
                $message = $this->_messageFail;
        }
        $this->view->message = $message;
    }

    public function setshuffleAction()
    {
        $message = null;
        if ($this->_request->isPost()) {
            $shuffle = $this->_playlistModel->setShuffle(
                $this->_request->getPost('name'),
                $this->_request->getPost('shuffle')
            );
            if ($shuffle)
                $message = array($this->view->t('Setting saved'), true);
            else
                $message = $this->_messageFail;
        }
        $this->view->message = $message;
    }

    public function setcurrentAction()
    {
        $message = null;
        if ($this->_request->isPost()) {
            try {
                $this->_playlistModel->setCurrentTrack(
                    $this->_request->getPost('name'),
                    $this->_request->getPost('current')
                );
                $message = array($this->view->t('Success'), true);
            } catch(Zend_Exception $e) {
                $message = array(
                    $this->view->t('Something went wrong: ') . $e->getMessage(),
                    'error'
                );
            }
        }
        $this->view->message = $message;
    }

    public function newAction()
    {
        $form = new Form_CreatePlaylist();
        if (!$this->_request->isPost() &&
                $form->isValid($this->_request->getParams())) {
            $name = $this->_request->getParam('name');
            $public = $this->_request->getParam('public');

            $row = $this->_playlistModel->create(
                $name, $public ? 'public' : 'private'
            );
            if($row) {
                $this->view->message = array(
                    $this->view->t('Playlist created'),
                    'success',
                    $row->id
                );
            } else {
                $this->view->message = array(
                    $this->view->t('Error creating playlist'),
                    'error'
                );
            }
        }
        $this->view->form = $form;
    }

    public function removeAction()
    {
        $message = array();

        if ($this->_request->isPost() &&
            ($id = $this->_request->getPost('id')) !== null) {
            $userRow = $this->_getUserRow();
            if ($userRow->countPlaylists() <= 1) {
                $message = array(
                    $this->view->t('You must have at least one playlist'),
                    'error'
                );
            } elseif (($msg = $this->_playlistModel->remove($id)) === true) {
                $message = array($this->view->t('Playlist removed'), 'success');
            } else {
                $message = array(
                    $this->view->t('Could not remove') . ': ' . $msg, 'error'
                );
            }

            $this->view->message = $message;
        }
    }

    public function privacyAction()
    {
        $message = $this->_messageFail;
        if ($this->_request->isPost() &&
            ($name = $this->_request->getPost('name')) !== null &&
            ($public = $this->_request->getPost('public')) !== null) {
            if ($this->_playlistModel->setPublic($name, $public))
                $message = array($this->view->t('Setting saved'), 'success');
        }
        $this->view->message = $message;
    }

    public function editnameAction()
    {
        $message = $this->_messageFail;
        if (
            $this->_request->isPost() &&
            ($name = $this->_request->getPost('name')) !== null &&
            ($newName = $this->_request->getPost('newname')) !== null
            ) {
            if ($this->_playlistModel->setNewName($name, $newName))
                $message = array($this->view->t('Saved'), 'success');
        }

        $this->view->message = $message;
    }

    public function voteAction()
    {
        $message = array('Invalid request', 'error');
        if (($trackId = $this->_request->getParam('track_id')) !== null &&
            (($bond = $this->_request->getParam('bond')) !== null) &&
            (
                $artistMusicTitleId
                = $this->_request->getParam('artist_music_title_id')
            ) !== null) {
            if (null === $this->_musicTrackLinkModel->bond(
                $artistMusicTitleId, $trackId, $this->_bondModel->{$bond}
            ))
                $message = array('Error while registering vote', 'error');
            else
                $message = array(
                    'Vote saved!! Thank you for your help!', 'success'
                );
        };

        $this->view->message = $message;
    }

    public function listAction()
    {
        $userRow = $this->_getUserRow();
        $this->view->currentId = $userRow->currentPlaylistId;
        DZend_Session_Namespace::close();
        $this->view->playlistRowSet = $this->_playlistModel->fetchAllUsers();
    }
}
