<?php

class IncboardController extends DZend_Controller_Action
{
    public function indexAction()
    {
        $this->view->form = new Form_IncBoardIntro();
        if ($this->_request->getParam('num') !== null) {
            $this->view->num = $this->_request->getParam('num');
        }
    }
}
