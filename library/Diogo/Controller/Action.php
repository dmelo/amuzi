<?php

class Diogo_Controller_Action extends Zend_Controller_Action
{
    public function init()
    {
        if($this->getRequest()->isXmlHttpRequest())
            $this->_helper->layout->disableLayout();
    }
}
