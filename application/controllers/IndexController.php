<?php
/**
 * IndexController
 *
 * @package You2Better
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
    public function indexAction()
    {
        $this->view->form = new Form_Search();
        if(isset($this->_session->user))
            $this->view->userId = $this->_session->user->id;
    }

    /**
     * searchAction
     *
     * @return void
     */
    public function searchAction()
    {
        // action body
    }

    /**
     * apiAction
     *
     * @return void
     */
    public function apiAction()
    {
        // action body
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
     * incboardAction IncBoard technique.
     *
     * @return void
     */
    public function incboardAction()
    {
        $this->view->form = new Form_Search();
    }

    public function testAction()
    {
        // action body
    }
}




