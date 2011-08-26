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
        /* Initialize action controller here */
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
        var_dump($q);
        if ($q !== null) {
            $youtube = new Youtube();
            $youtube->search($q);
        }
    }


}



