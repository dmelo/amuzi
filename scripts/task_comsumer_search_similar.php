<?php

require_once '../scripts/lib.php';

$taskSetModel = new TaskSet();
$musicSimilarityModel = new MusicSimilarity();

$rowSet = $taskSetModel->findOpenTasks('SearchSimilar');

foreach ($rowSet as $row) {
    echo $row->id . "#" . $row->param0 . "#" . $row->param1 . "#" . PHP_EOL;
    $ret = $musicSimilarityModel->searchSimilarSync($row->param0, $row->param1);
    echo print_r($ret[0], true) . PHP_EOL;
    $done = date('Y-m-d H:i:s', time());
    $expiration = date('Y-m-d H:i:s', strtotime($done . ' + 1 month'));

    $row->done = $done;
    $row->expiration = $expiration;
    $row->save();

    break;
}
