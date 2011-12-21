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
    protected $session;
    protected $request;
    protected $playlistModel;

    public function init()
    {
        $this->session = new Zend_Session_Namespace('session');
        $this->request = $this->getRequest();
        $this->playlistModel = new Playlist();
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
        if($this->request->isPost()) {
            $this->session->playlist = array($this->request->getPost('playlist'), $this->request->getPost('name'));
            if(isset($this->session->user))
                $playlistModel->import($this->session->playlist[0], $this->session->playlist[1]);
        }
    }

    public function addAction()
    {
        if($this->request->isPost()) {
        }
    }

    /**
     * loadAction Loads the user's playlist.
     *
     * @return void
     */
    public function loadAction()
    {
        $session = new Zend_Session_Namespace('session');
        $request = $this->getRequest();

        if($request->isPost()) {
            if(isset($session->user)) {
                $playlistModel = new Playlist();
                $name = $request->getPost('name');
                $this->view->playlist = array($playlistModel->export($name), $name);
            }
            elseif(isset($session->playlist))
                $this->view->playlist = $session->playlist;
            else
                $this->view->playlist = null;
        }
    }
}
