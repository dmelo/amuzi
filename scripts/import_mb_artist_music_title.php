<?php

require_once '../scripts/lib.php';

$c = new DZend_Chronometer();

$artistMusicTitleModel = new ArtistMusicTitle();
$i = 0;
if (($fd = fopen('/tmp/artist_track.csv', 'r')) !== false) {
    while (!feof($fd)) {
        $row = fgetcsv($fd);
        $id = $artistMusicTitleModel->insert($row[0], $row[1]);
        if (++$i % 100 === 0) {
            $time = time();
            echo "import_mb_artist_music_title {$time}: {$row[0]}, {$row[1]}, $id" . PHP_EOL;
        }
    }
}
