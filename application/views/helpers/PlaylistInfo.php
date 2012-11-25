<?php

class View_Helper_PlaylistInfo extends View_Helper_T
{
    private function _secsToTime($secs)
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

    private function _trackRow($trackRow)
    {
        return '<li><img src="' . $trackRow['cover'] . '"/> <span class="title">'
            . $trackRow['title'] . '</span> <span class="duration">'
            . $this->_secsToTime($trackRow['duration']) . '</span></li>';
    }

    public function playlistInfo($playlistRow)
    {
        $trackList = $playlistRow->getTrackListAsArray();
        $ret = '<div class="playlist-info">'
            . '<div class="head">'
            . '<p>' . $this->t('Name') . ': ' . $playlistRow->name . '</p>'
            . '<p>' . $this->t('Nro Tracks') . ': ' . count($trackList) . '</p>'
            . '<p>' . $this->t('Play time') . ': ' . $this->_secsToTime($playlistRow->playTime())
            . '</p>'
            . '</div>'
            . '<div class="body"><ul>';
        foreach ($trackList as $trackRow) {
            $ret .= $this->_trackRow($trackRow);
        }
        $ret .= '</ul></div></div>';

        return $ret;
    }
}
