<?php

/**
 * 
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
$baseUrl = $argv[1];
$url = $baseUrl . '/artist/Coldplay';

while (1) {
    echo "visiting $url..." . PHP_EOL;
    $content = file_get_contents($url);
    $dom = new DOMDocument();
    $dom->loadHTML($content);
    $xpath = new DomXpath($dom);
    $links = $xpath->query('//*[@class="item-square playlist-square object-playlist"]');
    $i = rand(0, $links->length);
    echo $links->length . ' links found' . PHP_EOL;
    $a = $links->item($i)->getElementsByTagName('a');
    $a = $a->item(0);
    $url = $baseUrl . $a->getAttribute('href');
    echo "sleep..." . PHP_EOL;
    sleep(60);
}
