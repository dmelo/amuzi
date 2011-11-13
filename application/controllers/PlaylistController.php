<?php

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
            $session->playlist = $request->getPost('playlist');
            if(isset($session->user)) {
                $playlistModel = new Playlist();
                $playlistModel->import($session->playlist, 'default');
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
        if(isset($session->user)) {
            $playlistModel = new Playlist();
            $this->view->playlist = $playlistModel->export('default');
        }
        elseif(isset($session->playlist))
            $this->view->playlist = $session->playlist;
        else
            $this->view->playlist = null;
    }
}
