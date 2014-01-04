<?php

require_once '../scripts/lib.php';

$musicSimilarityModel = new MusicSimilarity();
$lastfmModel = new Lastfm();
$artistMusicTitleModel = new ArtistMusicTitle();

$artist = 'U2';
$musicTitle = 'One';

$rowSet = $lastfmModel->getSimilar($artist, $musicTitle);
$artistMusicTitleId = $artistMusicTitleModel->insert(
    $artist, $musicTitle
);
$artistMusicTitleIdList[] = $artistMusicTitleId;
$list = array(array(
    'artist' => $artist,
    'musicTitle' => $musicTitle,
    'artistMusicTitleId' => $artistMusicTitleId
));

echo "rowSet" . PHP_EOL;
print_r($rowSet);


$rowSet2 = count($rowSet) > 0 ?
    $artistMusicTitleModel->insertMulti($rowSet) : array();

echo "rowSet2" . PHP_EOL;
print_r($rowSet2);

$rowSet3 = count($rowSet2) > 0 ?
    $musicSimilarityModel->insertMulti($artistMusicTitleId, $rowSet2) : array();

echo "rowSet3" . PHP_EOL;
print_r($rowSet3);


$rowSet4 = $musicSimilarityModel->findByArtistMusicTitleIdAndDegree($artistMusicTitleId);
$count = 0;
foreach ($rowSet4 as $musicSimilarityRow) {
    $id = 0;
    if ($artistMusicTitleId == $musicSimilarityRow->fArtistMusicTitleId) {
        $id = $musicSimilarityRow->sArtistMusicTitleId;
    } else {
        $id = $musicSimilarityRow->fArtistMusicTitleId;
    }
    echo "working on $count id $id" . PHP_EOL;

    $artistMusicTitleRow = $artistMusicTitleModel->findRowById($id);
    $rowSet = $lastfmModel->getSimilar(
        $artistMusicTitleRow->getArtistName(),
        $artistMusicTitleRow->getMusicTitleName()
    );
    $rowSet2 = count($rowSet) > 0 ?
        $artistMusicTitleModel->insertMulti($rowSet) : array();
    $rowSet3 = count($rowSet) > 0 ?
        $musicSimilarityModel->insertMulti($id, $rowSet2) : array();
    $count++;
}
