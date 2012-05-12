<?php

class Auth_Model_Form_ForgotPassword extends DZend_Form
{
    public function init()
    {
        $this->addEmail();
        $this->addSubmit($this->_t('Send'));
        $this->setMethod('post');
    }
}
