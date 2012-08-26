<?php

require_once '../scripts/lib.php';

$logger = Zend_Registry::get('logger');

$logger->info('calc_similarity_degree_1.php');

$musicSimilarityModel = new MusicSimilarity();

while (1) {
    $artistMusicTitleId = $musicSimilarityModel->getRandomArtistMusicTitleId();
    print_r($musicSimilarityModel->calcSimilarityDegree($artistMusicTitleId, 1));


}
