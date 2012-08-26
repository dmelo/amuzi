<?php

require_once '../scripts/lib.php';

$logger = Zend_Registry::get('logger');

$logger->info('calc_similarity_degree_1.php');

$musicSimilarityModel = new MusicSimilarity();

while (1) {
    $artistMusicTitleId = $musicSimilarityModel->getRandomArtistMusicTitleId();
    $rowSet = $musicSimilarityModel->findByArtistMusicTitleIdAndDegree($artistMusicTitleId);
    $ids = array();
    $similarities = array();
    $newRows = array();
    foreach ($rowSet as $row) {
        $ids[] = $row->fArtistMusicTitleId == $artistMusicTitleId ? $row->sArtistMusicTitleId : $row->fArtistMusicTitleId;
        $similarities[] = $row->similarity;
    }

    for ($i = 0; $i < count($ids); $i++)
        for ($j = $i + 1; $j < count($ids); $j++)
            if (($similarity = ($similarities[$i] * $similarities[$j]) / 10000) > 25)
                $newRows[] = $musicSimilarityModel->packData($ids[$i], $ids[$j], $similarity, 1);

    if (count($newRows) > 0) {
        echo 'trying to insert ' . count($newRows) . PHP_EOL;
        $ret = $musicSimilarityModel->insertTree($newRows);
        echo 'requests: ' . $ret[0] . PHP_EOL;
        echo 'rows inserted: ' . $ret[1] . PHP_EOL . PHP_EOL;
    }
}
