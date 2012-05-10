<?php

class Auth_Model_Form_ResetPassword extends DZend_Form
{
    public function init()
    {
        $element = new Zend_Form_Element_Password('passwordnew');
        $element->setRequired();
        $element->setAttrib('placeholder', $this->_t('******'));
        $element->setLabel($this->_t('New Password'));
        $this->addElement($element);

        $this->addConfirmPassword();
        $this->addSubmit($this->_t('Update Password'));

        $this->setMethod('post');
    }
}
