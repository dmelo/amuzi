<?php
/**
 * IndexController
 *
 * @package Amuzi
 * @version 1.0
 * @copyright Copyright (C) 2010 Diogo Oliveira de Melo. All rights reserved.
 * @author Diogo Oliveira de Melo <dmelo87@gmail.com>
 * @license GPL version 3
 */
class IndexController extends DZend_Controller_Action
{
    /**
     * indexAction
     *
     * @return void
     */

    public function __construct(
        Zend_Controller_Request_Abstract $request,
        Zend_Controller_Response_Abstract $response,
        array $invokeArgs = array())
    {
        parent::__construct($request, $response, $invokeArgs);

        $this->view->form = new Form_Search();
        if(isset($this->_session->user))
            $this->view->userId = $this->_session->user->id;
    }

    public function indexAction()
    {
        if(!isset($this->_session->user))
            $this->_forward('login', 'index', 'Auth');
    }

    public function incboardAction()
    {
        $this->view->form->setAttrib('id', 'incboard-search');
        $this->view->form->setAction('/api/searchsimilar');
    }

    /**
     * aboutAction
     *
     * @return void
     */
    public function aboutAction()
    {
        // action body
    }

    /**
     * errorAction
     *
     * @return void
     */
    public function errorAction()
    {
        // action body
    }

    public function testAction()
    {
        $this->_request->setParam('kk', 'kk');
        var_dump($this->_request->getParams());
    }

}
