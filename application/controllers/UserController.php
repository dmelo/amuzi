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
        if($this->_request->getParam('facebook_id') != null) {
            $params = $this->_request->getParams();
            $user = new User();
            $this->view->message = $user->login($params);
            $this->_session->user = $user->findRowByFacebookId($this->_request->getParam('facebook_id'));
        }
    }

    /**
     * logoutAction Log the user out.
     *
     * @return void
     */
    public function logoutAction()
    {
        unset($this->_session->user);
    }

    public function indexAction()
    {
        $this->view->form = new Application_Form_UserSettings();
    }
}
