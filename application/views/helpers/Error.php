<?php

class View_Helper_Error extends View_Helper_T
{
    public function error($str = null)
    {
        $str = null === $str ? 'Invalid request' : $str;
        return '<span>' . $this->t('Error:') . ' ' . $this->t($str) . '</span>';
    }
}
