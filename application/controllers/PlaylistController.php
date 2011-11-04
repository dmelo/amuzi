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
        }
    }

    public function loadAction()
    {
        $session = new Zend_Session_Namespace('session');
        $this->view->playlist = isset($session->playlist) ? $session->playlist : null;
    }
}
