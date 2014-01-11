<?php

/**
 * View_Helper_LightSquare
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
require_once('views/helpers/T.php');
class View_Helper_LightSquare extends View_Helper_T
{
    public function lightSquare($href, $name, $img, $isAjax = false)
    {
        return '<div class="item-square playlist-square object-playlist" >'
            . '<div class="cover"><a href="' . $href . '" ' . ($isAjax ? 'class="loadModal" ' : '') . '><img alt="' . htmlspecialchars($name) . '" src="' . $img
            . '"/></a></div>' . '<div class="name">' . ucfirst(htmlspecialchars($name))
            . '</div>'
            . '</div>';
    }
}
