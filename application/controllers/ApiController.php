<?php

/**
 * ApiController
 *
 * @version 1.0
 * @copyright Copyright (C) 2011 Diogo Oliveira de Melo. All rights reserved.
 * @author Diogo Oliveira de Melo <dmelo87@gmail.com>
 * @license GPL
 */
class ApiController extends Zend_Controller_Action
{

    /**
     * init
     *
     * @return void
     */
    public function init()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
    }

    /**
     * indexAction
     *
     * @return void
     */
    public function indexAction()
    {
        // action body
    }

    /**
     * searchAction
     *
     * @return void
     */
    public function searchAction()
    {
        $q = $this->getRequest()->getParam('q');
        $list = array();
        if ($q !== null) {
            $youtube = new Youtube();
            $resultSet = $youtube->search($q);
            $item = array();
            foreach ($resultSet as $result) {
                $item['id'] = $result->id;
                $item['title'] = $result->title;
                $item['content'] = $result->content;
                $item['you2better'] = $result->you2better;
                $item['pic'] = $result->pic;
                $list[] = $item;
            }

            $this->view->output = $list;
        }
    }

    /**
     * postDispatch
     *
     * @return void
     */
    public function postDispatch()
    {
        if(isset( $this->view->output ))
            echo Zend_Json::encode($this->view->output);
    }
}
