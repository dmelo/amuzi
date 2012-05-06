<?php

class Auth_IndexController extends DZend_Controller_Action
{
    /**
     * loginAction Authenticate the user.
     *
     * @return void
     */
    public function loginAction()
    {
        var_dump(Zend_Auth::getInstance()->getIdentity());
        $form = new Auth_Model_Form_Login();
        $params = $this->_request->getParams();
        if ($this->_request->isPost() && $form->isValid($this->_request->getParams())) {
            $authAdapter = Zend_Registry::get('authAdapter');
            $authAdapter->setIdentity($params['email']);
            $authAdapter->setCredential($params['password']);
            $auth = Zend_Auth::getInstance();
            if($auth->hasIdentity())
                $auth->getIdentity();
            $result = $auth->authenticate($authAdapter);
            $message = null;
            if (Zend_Auth_Result::SUCCESS === $result->getCode()) {
                $this->_helper->redirector('index', 'index', 'default');
            } else {
                $message = array($this->view->t("Invalid email and/or password."), "error");
            }
            if(null !== $message)
                $this->view->message = $message;
        }
        $this->view->form = $form;
    }

    /**
     * logoutAction Disassociate the user.
     *
     * @return void
     */
    public function logoutAction()
    {
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
            $auth->clearIdentity();
    }

    /**
     * registerAction Creates a new user account.
     *
     * @return void
     */
    public function registerAction()
    {
        $form = new Auth_Model_Form_Register();
        $message = null;
        $params = $this->_request->getParams();
        $userModel = new User();
        if($this->_request->isPost() && $form->isValid($params)) {
            if($params['password'] !== $params['password2'])
                $message = array($this->view->t('Password doesn\'t match'), 'error');
            elseif(($userRow = $userModel->findByEmail($params['email'])) !== null)
                $message = array($this->view->t('Email is already registered'), 'error');
            else {
                if($userModel->register($params['name'], $params['email'], $params['password']) === true) {
                    $userRow = $userModel->findByEmail($params['email']);
                    if($userModel->sendActivateAccountEmail($userRow))
                        $message = array($this->view->t('User registered. Check your email to activate your account.'), 'success');
                    else {
                        $message = array($this->view->t('An error occurred. It was not possible to send the email. Plase try again'), 'error');
                        $userModel->deleteByEmail($params['email']);
                    }
                }
                else
                    $message = array($this->view->t('Some error occurred, please try again'), 'error');
            }

            if($message[1] !== 'success')
                $this->view->form = $form;
            $this->view->message = $message;
        } else
            $this->view->form = $form;
    }

    public function activateAction()
    {
        $email = $this->_request->getParam('email');
        $token = Zend_Filter::filterStatic($this->_request->getParam('token'), 'Alnum');

        $userModel = new User();
        $userRow = $userModel->findByEmail($email);
        $message = null;
        if(null === $userRow || '' === $userRow->token || $userRow->token !== $token) {
            $message = array($this->view->t('The email %s cannot be activated', $email), 'error');
        } else {
            $userRow->token = '';
            $userRow->save();
            $message = array('You account is activated. You can login now.', 'success');
        }

        $this->view->message = $message;
    }

    /**
     * forgotpasswordAction Send an email with an url that enable the user to.
     * reset his password.
     *
     * @return void
     */
    public function forgotpasswordAction()
    {
    }

    /**
     * resetpasswordAction Reset the user password.
     *
     * @return void
     */
    public function resetpasswordAction()
    {
    }
}
