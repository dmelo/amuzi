<?php

class ShareController extends DZend_Controller_Action
{
    public function indexAction()
    {
        if($this->_request->isPost()) {
            $this->view->url = $this->_request->getPost('url');
        }
    }
}
