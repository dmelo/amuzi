<?php

require_once '../scripts/lib.php';

$a = new AmuziSearch();
$i = 3;
$ret = $a->autocomplete("james bond", "track");

foreach ($ret as $row) {
    echo $row->name . PHP_EOL;
}


