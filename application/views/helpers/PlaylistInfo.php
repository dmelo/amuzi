<?php

/**
 * View_Helper_PlaylistInfo
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
require_once('views/helpers/T.php');
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
        if (count($trackRow) > 2) {
            return '<li><img src="' . $trackRow['cover'] . '"/> <span class="title">'
                . $trackRow['title'] . '</span> <span class="duration">'
                . $this->_secsToTime($trackRow['duration']) . '</span></li>';
        } else {
            return '';
        }
    }

    public function playlistInfo($playlistRow)
    {
        $trackList = $playlistRow->getTrackListAsArray();
        $ret = '<div class="head">'
            . '<p>' . $this->t('Name') . ': ' . $playlistRow->name . '</p>'
            . '<p>' . $this->t('Nro Tracks') . ': ' . count($trackList) . '</p>'
            . '<p>' . $this->t('Play time') . ': ' . $this->_secsToTime($playlistRow->playTime())
            . '</p>'
            . '</div>'
            . '<div class="body"><ol>';
        foreach ($trackList as $trackRow) {
            $ret .= $this->_trackRow($trackRow);
        }
        $ret .= '</ol></div>';

        return $ret;
    }
}
