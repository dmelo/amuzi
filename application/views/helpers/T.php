<?php

class View_Helper_T extends Zend_View_Helper_Abstract
{
    public $view;

    public function t($arg)
    {
        return $this->view->translate->_($arg);
    }

    public function setView(Zend_View_Interface $view)
    {
        $this->view = $view;
    }
}
