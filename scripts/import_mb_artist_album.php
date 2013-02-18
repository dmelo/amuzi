<?php

require_once '../scripts/lib.php';

$albumModel = new Album();
$i = 0;
if (($fd = fopen('/tmp/artist_album.csv', 'r')) !== false) {
    while (!feof($fd)) {
        $row = fgetcsv($fd);
        $id = $albumModel->insertEmpty($row[0], $row[1]);
        if (++$i % 100 === 0) {
            $time = time();
            echo "import_mb_artist_album {$time}: {$row[0]}, {$row[1]}, $id" . PHP_EOL;
        }
    }
}
