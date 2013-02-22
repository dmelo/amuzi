<?php

require_once '../scripts/lib.php';

$a = new AmuziSearch();
$i = 3;
$q = 'james bond';
$ret = array_merge($a->autocomplete($q, "track"), $a->autocomplete($q, 'album'));

foreach ($ret as $row) {
    echo $row->name . PHP_EOL;
}


