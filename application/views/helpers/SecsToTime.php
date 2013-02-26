<?php

class View_Helper_SecsToTime extends View_Helper_T
{
    public function secsToTime($secs)
    {
        $ret = '';

        if ($secs < 60) {
            return $secs . 's';
        } elseif ($secs < 3600) {
            return sprintf('%d:%02d', (int) $secs / 60, $secs % 60);
        } else {
            $h = (int) $secs / 3600;
            $m = (int) ($secs - ($h * 3600)) / 60;
            $s = $secs % 60;
            return sprintf('%d:%02d:%02d', $h, $m, $s);
        }
    }
}
