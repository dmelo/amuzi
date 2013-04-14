<?php

/**
 * IndexController
 *
 * @package Amuzi
 * @version 1.0
 * Amuzi - Online music
 * Copyright (C) 2010-2013  Diogo Oliveira de Melo
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
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
        if (isset($this->_session->user)) {
            $this->view->user = $this->_session->user;
        }
    }

    public function indexAction()
    {
    }

    public function incboardAction()
    {
        $this->view->form->setAttrib('id', 'incboard-search');
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
        try {
            print_r($this->_amuziSearchModel->autocomplete('the', 'track'));
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function helpAction()
    {
    }

    public function innerloginAction()
    {
    }
}
