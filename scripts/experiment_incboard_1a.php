<?php

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

/** Zend_Application */
require_once 'Zend/Application.php';
require_once 'Zend/Loader/Autoloader.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

$application->getBootstrap()->bootstrap();

function mapInsert($value, $map)
{
    if(array_search($value, $map) === FALSE)
        $map[] = $value;

    return $map;
}

$musicSimilarityModel = new MusicSimilarity();

$artistMusicTitleIds = array(202, 177, 535, 717, 918);

foreach ($artistMusicTitleIds as $artistMusicTitleId) {
    $rowSet = $musicSimilarityModel->findByArtistMusicTitleIdAndDegree(
        $artistMusicTitleId, 0
    );

    $ids = array();
    foreach ($rowSet as $row) {
        $ids = mapInsert($row->fArtistMusicTitleId, $ids);
        $ids = mapInsert($row->sArtistMusicTitleId, $ids);

        if (count($ids) >= 98)
            break;
    }

    $completeRowSet = $musicSimilarityModel->findByArtistMusicTitleIdSetAndDegree($ids, 0);
    $similarityMap = array();
    $nroSimilar = 0;
    echo "artistMusicTitleId: $artistMusicTitleId" . PHP_EOL;

    foreach ($completeRowSet as $row) {
        if (!array_key_exists($row->fArtistMusicTitleId, $similarityMap))
            $similarityMap[$row->fArtistMusicTitleId] = array();
        $similarityMap[$row->fArtistMusicTitleId][$row->sArtistMusicTitleId] =
            $row->similarity;

        if (array_search($row->fArtistMusicTitleId, $ids) !== FALSE &&
            array_search($row->sArtistMusicTitleId, $ids) !== FALSE) {

            $nroSimilar++;
        }
    }

    $nroIds = count($ids);
    $percentSimilar = ($nroSimilar * 100) / (($nroIds * ($nroIds - 1) ) / 2);
    echo "nroSimilar: ${nroSimilar}" . PHP_EOL;
    echo "% Similar: ${percentSimilar}" . PHP_EOL . PHP_EOL;
}
