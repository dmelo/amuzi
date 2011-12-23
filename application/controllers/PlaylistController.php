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
                $playlistModel->import($this->_session->playlist[0], $this->_session->playlist[1]);
        }
    }

    public function addAction()
    {
        if($this->_request->isPost()) {
        }
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
                $this->view->playlist = array($playlistModel->export($name), $name);
            }
            elseif(isset($this->_session->playlist))
                $this->view->playlist = $this->_session->playlist;
            else
                $this->view->playlist = null;
        }
    }
}
