<?php

require_once '../scripts/lib.php';

$artistMusicTitleIds = array(202, 177, 535, 717, 918);


foreach($artistMusicTitleIds as $artistMusicTitleId) {
    list($nroSimilar, $percentSimilar) = countSimilaritiesOnIncBoard($artistMusicTitleId);
    echo "nroSimilar: ${nroSimilar}" . PHP_EOL;
    echo "% Similar: ${percentSimilar}" . PHP_EOL . PHP_EOL;
}
