<?php

class Diogo_Form extends Zend_Form
{
    protected $_translate;

    protected function _t($arg)
    {
        return $this->_translate->_($arg);
    }

    public function __construct($options = null)
    {
        $session = new Zend_Session_Namespace('session', true);
        $this->_translate = isset($session->translate) ? $session->translate : null;
        parent::__construct($options);
    }
}
