<?php

class Diogo_Form extends EasyBib_Form
{
    protected $_translate;
    protected $_useBootstrap;

    protected function _t($arg)
    {
        return $this->_translate->_($arg);
    }

    public function __construct($options = null)
    {
        $this->_useBootstrap = true;
        $session = new Zend_Session_Namespace('session', true);
        $this->_translate = isset($session->translate) ? $session->translate : null;
        parent::__construct($options);
        if($this->_useBootstrap)
            EasyBib_Form_Decorator::setFormDecorator($this, EasyBib_Form_Decorator::BOOTSTRAP, 'submit', 'cancel');
    }
}
