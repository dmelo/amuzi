<?php

class UserController extends DZend_Controller_Action
{
    public function init()
    {
        parent::init();
        if('index' == $this->_request->getActionName())
            $this->_loginRequired = true;
    }

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
        $form = new Form_UserSettings();
        $userModel = new User();

        if($this->_request->isGet() && $form->isValid($this->_request->getParams())) {
            $userModel->setSettings($this->_request->getParams());
            $this->view->message = true;
        } else {
            $form->populate($userModel->getSettings());
        }

        $this->view->form = $form;
    }
}
