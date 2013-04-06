<?php

class View_Helper_TrackListHtml extends Zend_View_Helper_Abstract
{
    public function trackListHtml($trackList)
    {
        $ret = '<ul>';
        $count = 1;
        foreach ($trackList as $track) {
            if (array_key_exists('title', $track)) {
                $ret .= '<li><img src="/img/play_icon_mini.png"/> '
                    . $count . ' - '. $track['title'] . '</li>';
                if ($count++ >= 5) {
                    break;
                }
            }
        }
        $ret .= '</ul>';

        return $ret;
    }
}
