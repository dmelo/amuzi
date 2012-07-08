<?php

class UserController extends DZend_Controller_Action
{
    public function init()
    {
        parent::init();
        if ('index' == $this->_request->getActionName())
            $this->_loginRequired = true;
    }

    public function indexAction()
    {
        $form = new Form_UserSettings();

        $params = $this->_request->getParams();
        if ($this->_request->isGet() && $form->isValid($params)) {
            $this->_userModel->setSettings($this->_request->getParams());
            $this->view->message = true;
        } else {
            $form->populate($this->_userModel->getSettings());
        }

        $this->view->form = $form;
    }
}
