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
        var_dump(Zend_Auth::getInstance()->getIdentity());
        var_dump($this->_session->user);
    }

}




