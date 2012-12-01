<?php

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
