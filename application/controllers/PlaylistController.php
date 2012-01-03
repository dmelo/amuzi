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
class PlaylistController extends Diogo_Controller_Action
{
    protected $_session;
    protected $_request;
    protected $_playlistModel;

    public function init()
    {
        $this->_session = new Zend_Session_Namespace('session');
        $this->_request = $this->getRequest();
        $this->_playlistModel = new Playlist();
        $this->_helper->layout->disableLayout();
    }

    public function indexAction()
    {
        // action body
    }

    /**
     * saveAction Save the playlist and link it to the user currently logged
     * in.
     *
     * @return void
     */
    public function saveAction()
    {
        if($this->_request->isPost()) {
            $this->_session->playlist = array($this->_request->getPost('playlist'), $this->_request->getPost('name'));
            if(isset($this->_session->user))
                $this->_playlistModel->import($this->_session->playlist[0], $this->_session->playlist[1]);
        }
    }

    public function addtrackAction()
    {
        $message = null;

        if($this->_request->isPost()) {
            $trackInfo = array('title' => $this->_request->getPost('title'),
                'mp3' => $this->_request->getPost('mp3'));
            try {
                $this->_playlistModel->addTrack($trackInfo, $this->_request->getPost('playlist'));
                $message = array($this->view->t('Track added'), true);
            } catch(Zend_Exception $e) {
                $message = array($this->view->t('Problems adding the track: ') + $e->getMessage(), false);
            }
        }

        $this->view->message = $message;
    }

    public function rmtrackAction()
    {
        $message = null;

        if($this->_request->isPost()) {
            $url = $this->_request->getPost('url');
            $playlist = $this->_request->getPost('playlist');
            try {
                $this->_playlistModel->rmTrack($url, $playlist);
                $message = array($this->view->t('Track removed'), true);
            } catch(Zend_Exception $e) {
                $message = array($this->view->t('Problems removing the track: ') + $e->getMessage(), false);
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
        if($this->_request->isPost()) {
            if(isset($this->_session->user)) {
                $playlistModel = new Playlist();
                $name = $this->_request->getPost('name');
                $this->view->playlist = $playlistModel->export($name);
            }
            elseif(isset($this->_session->playlist))
                $this->view->playlist = $this->_session->playlist;
            else
                $this->view->playlist = null;
        }
    }

    public function setrepeatAction()
    {
        $message = null;
        if($this->_request->isPost()) {
            if($this->_playlistModel->setRepeat($this->_request->getPost('repeat')))
                $message = array($this->view->t('Setting saved'), true);
            else
                $message = array($this->view->t('Failed saving setting'), false);
        }
        $this->view->message = $message;
    }

    public function setshuffleAction()
    {
        $message = null;
        if($this->_request->isPost()) {
            if($this->_playlistModel->setShuffle($this->_request->getPost('shuffle')))
                $message = array($this->view->t('Setting saved'), true);
            else
                $message = array($this->view->t('Failed saving setting'), false);
        }
        $this->view->message = $message;
    }
}
