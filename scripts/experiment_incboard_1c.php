<?php

require_once '../scripts/lib.php';

$logger = Zend_Registry::get('logger');
$baseUrl = 'http://amuzi.localhost';
$artistMusicTitleModel = new ArtistMusicTitle();
$musicSimilarityModel = new MusicSimilarity();

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
    print_r($musicSimilarityModel->calcSimilarityDegree($artistMusicTitleId));

    $i = 0;

    list($nroSimilar, $percentSimilar) = countSimilaritiesOnIncBoard($artistMusicTitleId, false);

    $msg = 'Experiment1. artist_music_id ' . $artistMusicTitleId . " similar nro: $i. nroSimilars: $nroSimilar. percentSimilar: $percentSimilar";
    echo $msg . PHP_EOL;
    $logger->info($msg);
}
