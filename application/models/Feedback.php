<?php

class Feedback extends DZend_Model
{
    public function insert($subject, $anonymous, $comment)
    {
        if (isset($this->_session->user)) {
            $data = array(
                'user_id' => $anonymous ? null : $this->_session->user->id,
                'subject' => $subject,
                'comment' => $comment
            );

            try {
                return $this->_feedbackDb->insert($data);
            } catch (Zend_Db_Statement_Exception $e) {
                return $e;
            }
        }
    }
}
