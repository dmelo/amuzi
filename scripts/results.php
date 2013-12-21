<?php

require_once '../scripts/lib.php';

$logModel = new Log();
$logRowSet = $logModel->findFilteredByLogActionId(array(1, 2));
$incBoard = $default = array();

foreach ($logRowSet as $logRow) {
    if ('default' === $logRow->view) {
        $default[$logRow->userId]++;
        $defaultTotal++;
    } elseif ('incboard' === $logRow->view) {
        $incBoard[$logRow->userId]++;
        $incBoardTotal++;
    }
}

echo "default: " . print_r($default, true) . ". incBoard: " . print_r($incBoard, true) . "." . PHP_EOL;
echo "defaultTotal: $defaultTotal. incBoardTotal: $incBoardTotal" . PHP_EOL;
