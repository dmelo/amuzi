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
        // action body

        $q = $this->getRequest()->getParam('q');
        $list = array();
        if ($q !== null) {
            $youtube = new Youtube();
            $resultSet = $youtube->search($q);
            $item = array();
            foreach( $resultSet as $result ) {
                $item['id'] = preg_replace('/http:\/\/gdata.youtube.com\/.*\//',
                '', $result->id);
                $item['title'] = $result->title;
                $item['content'] = $result->content;
                $item['you2better'] = Zend_Registry::get('domain');
                $item['you2better'] .= '/api/' . $item['id'] . '/' .
                    $item['title'] . '.mp3';

            }
            $list[] = $item;

            $this->view->output = $list;
            var_dump( $this->view->output );
        }
    }

    public function postDispatch()
    {
        if(isset( $this->view->output ))
            echo Zend_Json::encode($this->view->output);
    }


}



