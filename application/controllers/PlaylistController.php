<?php

/**
 * PlaylistController Actions regarding playlist management and viewing.
 *
 * @package
 * @version 1.0
 * @copyright Copyright (C) 2010 Diogo Oliveira de Melo. All rights reserved.
 * @author Diogo Oliveira de Melo <dmelo87@gmail.com>
 * @license GPL version 3
 */
class PlaylistController extends DZend_Controller_Action
{
    protected $_playlistModel;
    protected $_messageFail;

    public function init()
    {
        parent::init();
        $this->_playlistModel = new Playlist();
        $this->_loginRequired = true;
        $this->_messageFail = array(
            $this->view->t('Failed saving setting'),
            false
        );
    }

    public function indexAction()
    {
        $this->view->form = new Form_PlaylistSettings();
        $this->view->resultSet = $this->_playlistModel->search('');
    }

    public function searchAction()
    {
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
            $this->_session->playlist = array(
                $this->_request->getPost('playlist'),
                $this->_request->getPost('name')
            );
            if (isset($this->_session->user))
                $this->_playlistModel->import(
                    $this->_session->playlist[0],
                    $this->_session->playlist[1]
                );
        }
    }

    /**
     * addtrackAction Receives a post request informing ( title, mp3 and
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
            if($this->_request->getPost('id') !== null)
                $trackInfo = array('id' => $this->_request->getPost('id'));
            else
                $trackInfo = array('title' => $this->_request->getPost('title'),
                    'mp3' => $this->_request->getPost('mp3'),
                    'cover' => $this->_request->getPost('cover'));
            try {
                $trackRow = $this->_playlistModel->addTrack(
                    $trackInfo,
                    $this->_request->getPost('playlist')
                );
                $message = array(
                    $this->view->t('Track added'), true, $trackRow->toArray()
                );
            } catch(Zend_Exception $e) {
                $message = array(
                    $this->view->t('Problems adding the track') . ': ' .
                        $e->getMessage(),
                    false
                );
            }
        } else {
            $message = array(
                $this->view->t('Problems adding track') . ': ' .
                $this->view->t('Invalid request'),
                false
            );
        }

        $this->view->message = $message;
    }

    public function rmtrackAction()
    {
        $message = null;

        if ($this->_request->isPost()) {
            $url = $this->_request->getPost('url');
            $playlist = $this->_request->getPost('playlist');
            try {
                $this->_playlistModel->rmTrack($url, $playlist);
                $message = array($this->view->t('Track removed'), true);
            } catch(Zend_Exception $e) {
                $message = array(
                    $this->view->t('Problems removing the track: ') +
                        $e->getMessage(),
                     false
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
            if (isset($this->_session->user)) {
                $name = $this->_request->getPost('name');
                $this->view->playlist = $this->_playlistModel->export($name);
            }
            elseif (isset($this->_session->playlist))
                $this->view->playlist = $this->_session->playlist;
            else
                $this->view->playlist = null;
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
                    false
                );
            }
        }
        $this->view->message = $message;
    }

    public function newAction()
    {
        $this->view->form = new Form_CreatePlaylist();
        $name = $this->_request->getParam('name');
        if (!$this->_request->isPost() && $name != null) {
            $row = $this->_playlistModel->create($name);
            if($row)
                $this->view->message = array($this->view->t('Playlist created'),
                    'success'
                );
            else
                $this->view->message = array(
                    $this->view->t('Error creating playlist'),
                    'error'
                );
        }
    }

    public function removeAction()
    {
        $message = array();

        if ($this->_request->isPost() &&
            ($name = $this->_request->getPost('name')) !== null) {
            if (($msg = $this->_playlistModel->remove($name)) === true)
                $message = array($this->view->t('Playlist removed'), 'success');
            else
                $message = array(
                    $this->view->t('Could not remove') . ': ' . $msg, 'error'
                );

            $this->view->message = $message;
        } else {
            $this->view->playlists = $this->_playlistModel->fetchAllUsers();
        }
    }
}
