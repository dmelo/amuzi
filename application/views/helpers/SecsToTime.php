<?php

/**
 * View_Helper_SecsToTime
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
