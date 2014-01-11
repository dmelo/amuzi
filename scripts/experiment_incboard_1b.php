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
require_once '../scripts/lib.php';

$logger = Zend_Registry::get('logger');
$baseUrl = 'http://amuzi.localhost';
$artistMusicTitleModel = new ArtistMusicTitle();

$logger->info('Starting experiment_incboard_1b.php');

$list = array(
    array('artist' => 'Muse', 'musicTitle' => 'Starlight'),
    array('artist' => 'U2', 'musicTitle' => 'One'),
    array('artist' => 'Frank Sinatra', 'musicTitle' => 'My Way'),
    array('artist' => 'Sonata Arctica', 'musicTitle' => 'My Land'),
    array('artist' => 'Ludwig van Beethoven', 'musicTitle' => 'Moonlight Sonata')
);

$urlSearchSimilar = $baseUrl . '/api/searchsimilar';

foreach($list as $artistMusic) {
    $params = array();
    $params['artist'] = $artistMusic['artist'];
    $params['musicTitle'] = $artistMusic['musicTitle'];
    $params['ajax'] = 1;
    $url = $baseUrl . '/api/searchmusic' . paramsToUri($params);
    $logger->debug('getting: ' . $url);
    $logger->debug("searchmusic: " . file_get_contents($url));

    $params['q'] = $artistMusic['artist'] . ' - ' . $artistMusic['musicTitle'];
    $url = $urlSearchSimilar . paramsToUri($params);
    $logger->debug("getting: $url");

    try {
        $content = file_get_contents($url);
        $similars = Zend_Json::decode($content);
    } catch(Zend_Json_Exception $e) {
        $logger->debug('Could not decode json: ' . $content);
        echo $e->getMessage() . PHP_EOL;
        echo $e->getTrace() . PHP_EOL;
    }

    $artistMusicTitleRow = $artistMusicTitleModel->findByArtistAndMusicTitle($artistMusic['artist'], $artistMusic['musicTitle']);
    $artistMusicTitleId = $artistMusicTitleRow->id;

    $i = 0;

    list($nroSimilar, $percentSimilar) = countSimilaritiesOnIncBoard($artistMusicTitleId);

    $msg = 'Experiment1. artist_music_id ' . $artistMusicTitleId . " similar nro: $i. nroSimilars: $nroSimilar. percentSimilar: $percentSimilar";
    echo $msg . PHP_EOL;
    $logger->info($msg);


    $i++;


    foreach($similars as $similar) {
        if ($i > 100)
            break;
        $url = $urlSearchSimilar . paramsToUri(array(
            'artist' => $similar['artist'],
            'musicTitle' => $similar['musicTitle'],
            'q' => $similar['artist'] . ' - ' . $similar['musicTitle'],
            'ajax' => 1
        ));

        $logger->debug("getting: $url");
        file_get_contents($url);

        list($nroSimilar, $percentSimilar) = countSimilaritiesOnIncBoard($artistMusicTitleId);

        $msg = 'Experiment1. artist_music_id ' . $artistMusicTitleId . " similar nro: $i. nroSimilars: $nroSimilar. percentSimilar: $percentSimilar";
        echo $msg . PHP_EOL;

        $i++;
    }
}
