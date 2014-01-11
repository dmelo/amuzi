<?php

/**
 * View_Helper_Vote
 *
 * @package Amuzi
 * @version 1.0
 * Amuzi - Online music
 * Copyright (C) 2010-2014  Diogo Oliveira de Melo
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
class View_Helper_Vote extends Zend_View_Helper_Abstract
{
    public function vote($trackId, $artistMusicTitleId, $bond)
    {
        $face = 'up' === $bond ? 'happy' : 'sad';
        return "<a class=\"vote vote-$bond\" href=\"/playlist/vote/track_id/"
            . "$trackId/artist_music_title_id/$artistMusicTitleId/bond/vote_"
            . "$bond\"><img src=\"/img/emotion_${face}_big.png\"/></a>";
    }
}
