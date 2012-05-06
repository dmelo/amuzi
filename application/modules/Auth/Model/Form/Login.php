<?php

class Auth_Model_Form_Login extends DZend_Form
{
    public function init()
    {
        $element = new Zend_Form_Element_Text('email');
        $element->setRequired();
        $element->setAttrib('placeholder', $this->_t("john.smith@gmail.com"));
        $element->setLabel($this->_t('Email'));
        $this->addElement($element);

        $element = new Zend_Form_Element_Password('password');
        $element->setRequired();
        $element->setAttrib('placeholder', "******");
        $element->setLabel($this->_t('Password'));
        $this->addElement($element);

        $element = new Zend_Form_Element_Submit('submit');
        $element->setLabel($this->_t('Login'));
        $this->addElement($element);

        $this->setMethod('post');
    }
}
