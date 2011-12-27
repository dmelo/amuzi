<?php

class UserController extends Diogo_Controller_Action
{
    /**
     * loginAction Logs the user in and save it on Zend_Session.
     *
     * @return void
     */
    public function loginAction()
    {
        $request = $this->getRequest();
        if($request->getParam('facebookId') != null) {
            $params = $this->getRequest()->getParams();
            $user = new User();
            $user->login($params);
            $session = new Zend_Session_Namespace('session');
            $session->user = $user->findRowByFacebookId($request->getParam('facebookId'));
        }
    }

    /**
     * logoutAction Log the user out.
     *
     * @return void
     */
    public function logoutAction()
    {
        $session = new Zend_Session_Namespace('session');
        unset($session->user);
    }

    public function indexAction()
    {
    }
}
