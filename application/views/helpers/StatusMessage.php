<?php

class View_Helper_StatusMessage extends Zend_View_Helper_Abstract
{
    public function statusMessage($message, $noAuto = false)
    {
        $piece  = $noAuto ? ' noauto="noauto" ' : '';

        return '<div style="display: none" id="status-message"' . $piece .
            '><p>' . $message[0] . '</p><span class="status">' . $message[1] .
            '</span></div>';
    }
}
