<?php

class Auth_Model_Form_Register extends DZend_Form
{
    public function init()
    {
        $element = new Zend_Form_Element_Text('name');
        $element->setRequired();
        $element->setAttrib('placeholder', $this->_t('John Smith'));
        $element->setLabel($this->_t('Name'));
        $this->addElement($element);

        $this->addEmail();
        $this->addPassword();
        $this->addConfirmPassword();
        $this->addSubmit($this->_t('Register'));

        $this->setMethod('post');
    }
}
