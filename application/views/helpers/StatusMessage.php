<?php

class View_Helper_StatusMessage extends Zend_View_Helper_Abstract
{
    public function statusMessage($message)
    {
        return '<div style="display: none" id="status-message"><p>' .
            $message[0] . '</p><span class="status">' . $message[1] .
            '</span></div>';
    }
}
