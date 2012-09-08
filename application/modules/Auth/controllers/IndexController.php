<?php

/**
 * Auth_IndexController
 *
 * @package Amuzi
 * @version 1.0
 * Amuzi - Online music
 * Copyright (C) 2010-2012  Diogo Oliveira de Melo
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
class Auth_IndexController extends DZend_Controller_Action
{
    protected $_userModel;

    public function init()
    {
        parent::init();
        $this->_userModel = new User();
    }

    /**
     * loginAction Authenticate the user.
     *
     * @return void
     */
    public function loginAction()
    {
        $form = new Auth_Model_Form_Login();
        $params = $this->_request->getParams();
        $message = null;

        if (
            $this->_request->isPost() &&
            $form->isValid($this->_request->getParams())
        ) {
            if (
                ($userRow =
                    $this->_userModel->findByEmail($params['email'])) === null
            ) {
                $message = array(
                    $this->view->t("Email not found. Are you new here?"),
                    'error'
                );

            } elseif ('' != $userRow->token) {
                $message = array(
                    $this->view->t(
                        "Acount not activated. Please, check your email"
                    ),
                    'error'
                );
            } else {
                $authAdapter = Zend_Registry::get('authAdapter');
                $authAdapter->setIdentity($params['email']);
                $authAdapter->setCredential($params['password']);
                $auth = Zend_Auth::getInstance();
                if($auth->hasIdentity())
                    $auth->getIdentity();
                $result = $auth->authenticate($authAdapter);
                $this->_logger->info("out of the IF");
                if (Zend_Auth_Result::SUCCESS === $result->getCode()) {
                    $this->_helper->redirector('index', 'index', 'default');
                } else {
                    $message = array(
                        $this->view->t("Wrong password."), "error"
                    );
                }
            }
        }

        $this->view->form = $form;
        if(null === $message && $this->_request->getParam('activated') == 1)
            $message = array(
                $this->view->t('Your account is active, you can LOGIN now'),
                'success'
            );
        if(null !== $message)
            $this->view->message = $message;
    }

    /**
     * logoutAction Disassociate the user.
     *
     * @return void
     */
    public function logoutAction()
    {
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            unset($this->_session->user);
            $auth->clearIdentity();
            $this->_helper->redirector('login', 'index', 'Auth');
        }
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
        if ($this->_request->isPost() && $form->isValid($params)) {
            if ($params['password'] !== $params['password2'])
                $message = array(
                    $this->view->t('Password doesn\'t match'), 'error'
                );
            elseif (($userRow 
                = $this->_userModel->findByEmail($params['email'])) !== null)
                $message = array(
                    $this->view->t('Email is already registered'), 'error'
                );
            else {
                if (
                    $this->_userModel->register(
                        $params['name'], $params['email'], $params['password']
                    ) === true
                ) {
                    $userRow = $this->_userModel->findByEmail($params['email']);
                    if ($this->_userModel->sendActivateAccountEmail($userRow)) {
                        $message = array($this->view->t(
                            'User registered. Check your '
                            . 'email to activate your account.'
                        ), 'success');
                        if (method_exists($userRow, 'postRegister'))
                            $userRow->postRegister();
                    } else {
                        $message = array($this->view->t(
                            'An error occurred. It was not possible to send '
                            . 'the email. Plase try again'
                        ), 'error');
                        $this->_userModel->deleteByEmail($params['email']);
                    }
                }
                else
                    $message = array($this->view->t(
                        'Some error occurred, please try again'
                    ), 'error');
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
        $token = Zend_Filter::filterStatic(
            $this->_request->getParam('token'), 'Alnum'
        );

        $userRow = $this->_userModel->findByEmail($email);
        $message = null;
        if (
            null === $userRow ||
            '' === $userRow->token ||
            $userRow->token !== $token
        ) {
            $message = array(
                $this->view->t('The email %s cannot be activated', $email),
                'error'
            );
        } else {
            $userRow->token = '';
            $userRow->save();
            $this->view->form = new Auth_Model_Form_Login();
            $message = array(
                'You account is activated. You can login now.', 'success'
            );
            $this->_helper->redirector(
                'login', 'index', 'Auth', array('activated' => '1')
            );
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
        $form = new Auth_Model_Form_ForgotPassword();
        $params = $this->_request->getParams();
        if ($this->_request->isPost() && $form->isValid($params)) {
            $userRow = $this->_userModel->findByEmail($params['email']);
            $message = array($this->view->t(
                'If this email is registered then you will receive an email ' .
                'that will allow you to edit your password'
            ), 'success');
            if ($userRow) {
                if (!$this->_userModel->sendForgotPasswordEmail($userRow))
                    $message = array($this->view->t(
                        'A problem occured while ' .
                        'trying to send your email. Please try again ' .
                        'later'
                    ), 'error');
            }
            $this->view->message = $message;
        }
        else
            $this->view->form = $form;
    }

    /**
     * resetpasswordAction Reset the user password.
     *
     * @return void
     */
    public function resetpasswordAction()
    {
        $params = $this->_request->getParams();
        $userRow = $this->_userModel->findByEmail($params['email']);
        if (
            $userRow &&
            $userRow->isForgotPasswordUrlValid($params['time'], $params['hash'])
        ) {
            $this->view->email = $params['email'];
            $form = new Auth_Model_Form_ResetPassword();

            if ($this->_request->isPost() && $form->isValid($params)) {
                if ($params['password2'] !== $params['passwordnew'])
                    $message = array(
                        $this->view->t('Passwords doesn\'t match'), 'error'
                    );
                elseif (strlen($params['passwordnew']) < 6)
                    $message = array(
                        $this->view->t('Password is too short'), 'error'
                    );
                else {
                    $userRow->password = sha1($params['passwordnew']);
                    $userRow->save();
                    $message = array($this->view->t(
                        'Password changed successfully'
                    ), 'success');
                    $this->_helper->redirector('index', 'index', 'default');
                }
                $this->view->message = $message;

                if('error' === $message[1])
                    $this->view->form = $form;
            }
            else
                $this->view->form = $form;
        }
    }
}
