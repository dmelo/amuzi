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
        $request = $this->getRequest();

        if($request->isPost()) {
            $session = new Zend_Session_Namespace('session');
            $session->playlist = array($request->getPost('playlist'), $request->getPost('name'));
            if(isset($session->user)) {
                $playlistModel = new Playlist();
                $playlistModel->import($session->playlist[0], $session->playlist[1]);
            }
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
