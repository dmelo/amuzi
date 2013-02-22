<?php

require_once '../scripts/lib.php';

$taskSetModel = new TaskSet();
$lastfmModel = new Lastfm();
$albumModel = new Album();

$rowSet = $taskSetModel->findOpenTasks('SearchString');

foreach ($rowSet as $row) {
    echo $row->id . '#' . $row->param0 . '#' . $row->param1 . '#' . PHP_EOL;
    if ('Album' === $row->param0) {
        $ret = $lastfmModel->searchAlbum($row->param1, 250);
        foreach ($ret as $r) {
            try {
                $albumModel->insertEmpty(
                    $r->artist,
                    $r->musicTitle,
                    $r->cover
                );
                echo "Inserted: " . $r . PHP_EOL;
            } catch (Zend_Exception $e) {
                echo "Album $r is already inserted" . PHP_EOL;
            }
        }
    } elseif ('MusicTitle' === $row->param0) {
        $track = $lastfmModel->searchTrack($row->param1, 250);
    }

    $done = date('Y-m-d H:i:s', time());
    $expiration = date('Y-m-d H:i:s', strtotime($done . ' + 6 month'));

    $row->done = $done;
    $row->expiration = $expiration;
    $row->save();

    break;
}

