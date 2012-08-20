<?php

require_once '../scripts/lib.php';

$logger = Zend_Registry::get('logger');
$baseUrl = 'http://amuzi.localhost';
$artistMusicTitleModel = new ArtistMusicTitle();

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
    $similars = Zend_Json::decode(file_get_contents($url));

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
