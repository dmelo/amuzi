<?php

class UserController extends Diogo_Controller_Action
{
    public function loginAction()
    {
        $request = $this->getRequest();
        if($request->getParam('facebookId') != null) {
            $params = $this->getRequest()->getParams();
            $user = new User();
            $user->login($params);
            $session = new Zend_Session_Namespace('session');
            $session->user = $user;
        }
    }

    public function logoutAction()
    {
    }

    public function indexAction()
    {
    }
}
