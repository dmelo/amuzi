<?php

class PlaylistController extends Diogo_Controller_Action
{
    public function indexAction()
    {
        // action body
    }

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
