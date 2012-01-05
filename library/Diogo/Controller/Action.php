<?php

class Diogo_Controller_Action extends Zend_Controller_Action
{
    protected $_session;
    protected $_request;

    public function init()
    {
        if($this->getRequest()->isXmlHttpRequest())
            $this->_helper->layout->disableLayout();

        try {
            $this->_session = new Zend_Session_Namespace('session');
        } catch(Zend_Session_Exception $e) {
            $this->_session = null;
        }

        $this->_request = $this->getRequest();
    }
}
