<?php

class Auth extends DZend_Model
{
    public function authenticate($email, $password)
    {
        $authAdapter = Zend_Registry::get('authAdapter');
        $authAdapter->setIdentity($email);
        $authAdapter->setCredential($password);

        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $auth->getIdentity();
        }

        return $auth->authenticate($authAdapter);
    }

    public function authenticateFacebook($email)
    {
        $authAdapter = new DZend_Auth_Adapter_Facebook();
        $authAdapter->setIdentity($email);

        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $auth->getIdentity();
        }

        return $auth->authenticate($authAdapter);
    }
}
