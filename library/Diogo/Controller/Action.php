<?php

class Diogo_Controller_Action extends Zend_Controller_Action
{
    protected $_session;
    protected $_request;

    public function init()
    {
        if($this->getRequest()->isXmlHttpRequest())
            $this->_helper->layout->disableLayout();

        $this->_session = Diogo_Session_Namespace::get('session');
        $this->_request = $this->getRequest();
    }
}
