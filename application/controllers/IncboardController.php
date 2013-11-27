<?php

class IncboardController extends DZend_Controller_Action
{
    public function init()
    {
        $js = Zend_Registry::get('js');
        $js[] = 'incboard-intro.js';
        Zend_Registry::set('js', $js);
        parent::init();
    }

    public function indexAction()
    {

        $this->view->form = new Form_IncBoardIntro();
        if ($this->_request->getParam('num') !== null) {
            $this->view->num = $this->_request->getParam('num');
        }
    }
}
