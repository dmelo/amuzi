<?php

/**
 * View_Helper_JPlayer
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
require_once 'views/helpers/T.php';
class View_Helper_JPlayer extends View_Helper_T
{
    public function _li($name)
    {
        $class = str_replace(' ', '-', $name);
        return "<li><a href=\"javascript:;\" class=\"jp-{$class}\" " .
            "tabindex=\"1\">{$name}</a></li>";
    }

    public function _liTwo($name)
    {
        $class = str_replace(' ', '-', $name);
        return "<li><a href=\"javascript:;\" class=\"jp-{$class}\" " .
            "tabindex=\"1\" title=\"{$name}\">{$name}</a></li>";
    }

    public function jPlayer($class = '')
    {
        $controls = array(
            'previous', 'play',
            'pause',
            'next',
            'stop',
            'mute',
            'unmute',
            'volume-max'
        );

        $toggles = array(
            'full-screen',
            'restore-screen',
            'shuffle',
            'shuffle-off',
            'repeat',
            'repeat-off'
        );

        echo '<div id="jp_container_1" class="jp-video jp-video-270p ' . $class . '">
        <div class="jp-type-playlist">
        <div id="jquery_jplayer_1" class="jp-jplayer"></div>
        <div class="jp-gui">
            <div class="jp-video-play">
                <a href="javascript:;" class="jp-video-play-icon" tabindex="1">
                    play
                </a>
            </div>
            <div class="jp-interface">
                <div class="jp-progress">
                    <div class="jp-seek-bar">
                        <div class="jp-play-bar"></div>
                    </div>
                </div>
                <div class="jp-current-time"></div>
                <div class="jp-duration"></div>
                <div class="jp-title">
                    <ul>
                        <li></li>
                        <li></li>
                    </ul>
                </div>
                <div class="jp-controls-holder">
                    <ul class="jp-controls">';
        foreach ($controls as $class)
            echo $this->_li($class);

        echo '
                    </ul>
                    <div class="jp-volume-bar">
                        <div class="jp-volume-bar-value"></div>
                    </div>
                    <ul class="jp-toggles">
                        <li><a href="#" class="share">' .
                            $this->t('share this') .
                        '</a></li>';
        foreach ($toggles as $class)
            echo $this->_liTwo($class);

        echo '
                    </ul>
                </div>
            </div>
        </div>
        <div class="jp-playlist">
            <ul>
                <!-- The method Playlist.displayPlaylist() uses this
                unordered list -->
                <li></li>
            </ul>
        </div>
        <div class="jp-no-solution">
        <span>' . $this->t('Update Required') . '</span>
            ' . $this->t(
    'To play the media you will need to either update your browser ' .
    'to a recent version or update your '
) .
            '<a href="http://get.adobe.com/flashplayer/" target="_blank">
                Flash plugin
            </a>.
        </div>
    </div>
</div>



<div id="jplayer_inspector_1"></div>';
    }
}
