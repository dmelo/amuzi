<?php

class Auth_Model_Form_Login extends DZend_Form
{
    public function init()
    {
        $this->addEmail();
        $this->addPassword();
        $this->addSubmit($this->_t('Login'));

        $this->setMethod('post');
        $this->setAction('/Auth/index/login');
    }
}
