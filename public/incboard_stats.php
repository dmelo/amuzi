<?php

$fd = fopen("tmp/incboard_stats.txt", "a");
fwrite($fd, $_GET['count'] . ',' . $_GET['threadshold'] . ',' . $_GET['num']
    . ',' . $_GET['time'] . ',' . $_GET['quality'] . PHP_EOL);
fclose($fd);
