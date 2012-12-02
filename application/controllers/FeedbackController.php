<?php

/**
 * FeedbackController
 *
 * @package Amuzi
 * @version 1.0
 * Amuzi - Online music
 * Copyright (C) 2010-2012  Diogo Oliveira de Melo
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
class FeedbackController extends DZend_Controller_Action
{
    public function indexAction()
    {
        $form = new Form_Feedback();
        if (
            $this->_request->isPost()
            && $this->_request->getPost('subject') != null
        ) {
            $data = $this->_request->getParams();
            $ret = $this->_feedbackModel->insert(
                $data['subject'],
                $data['anonymous'],
                $data['comment']
            );

            if ($ret instanceof Zend_Db_Statement_Exception) {
                $message = array('Error: ' . $ret->getMessage(), 'error');
            } else {
                $message = array('Feedback send. Thank you!', 'success');
            }
            $this->view->message = $message;
        }

        $this->view->form = $form;
    }
}
