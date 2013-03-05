<?php

/**
 * 
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
require_once '../scripts/lib.php';

$c = new DZend_Chronometer();
$c->start();
$artistMusicTitleModel = new ArtistMusicTitle();
$albumModel = new Album();
$q = 'blable';
$listMusicTitle = $artistMusicTitleModel->autocomplete($q);
$listAlbum = $albumModel->autocomplete($q);
$c->stop();

echo $c->get() . PHP_EOL;

