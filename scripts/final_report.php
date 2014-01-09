<?php

require_once '../scripts/lib.php';

$logModel = new Log();


$objMap = array(
    1 => 'album',
    2 => 'track'
);


function processLog($logRowSet)
{
    $userCount = array('default' => array(), 'incboard' => array());
    $userSet = array();
    $objCount = array(
        1 => array('default' => 0, 'incboard' => 0),
        2 => array('default' => 0, 'incboard' => 0),
        'default' => 0,
        'incboard' => 0,
    );

    foreach ($logRowSet as $logRow) {
        if (in_array($logRow->logActionId, array(1, 2)) && 'mozaic' !== $logRow->view) {
            $objCount[$logRow->logActionId][$logRow->view]++;
            $objCount[$logRow->view]++;
            if (!array_key_exists($logRow->userId, $userCount[$logRow->view])) {
                $userCount[$logRow->view][$logRow->userId] = 0;
            }
            if (!in_array($logRow->userId, $userSet)) {
                $userSet[] = $logRow->userId;
            }
            $userCount[$logRow->view][$logRow->userId]++;
        }
    }

    return array($objCount, $userCount, $userSet);
}





$logRowSet = $logModel->findAuditableLog();
list($objCount, $userCount, $userSet) = processLog($logRowSet);

echo "Elements added by each Search Mode" . PHP_EOL;
for ($objType = 1; $objType <= 2; $objType++) {
    $row = $objCount[$objType];
    foreach ($row as $searchMode => $val) {
        $obj = $objMap[$objType];
        echo "Obj $obj on $searchMode: $val" . PHP_EOL;
    }
}

echo PHP_EOL;



echo "# of users who tried each search mode" . PHP_EOL;
foreach ($userCount as $searchMode => $set) {
    echo "$searchMode: " . count($set) . PHP_EOL;
}
echo "Total # of users: " . count($userSet) . PHP_EOL;
echo PHP_EOL;

echo "ratio of objs added on each search mode per user" . PHP_EOL;
$r = array();
foreach ($userCount as $searchMode => $set) {
    $aux = $objCount[$searchMode] / count($set);
    $r[] = $aux;
    echo "$searchMode: " . $aux . PHP_EOL;
}
echo "comp: "  . ($r[1] / $r[0]) . PHP_EOL;

echo "Total # of auditable logs: " . count($logRowSet) . PHP_EOL . PHP_EOL . PHP_EOL;

// TODO: Amount of time spent on each search mode.
// TODO: Compare insertions and quality of similarity matrix.



if (array_key_exists(1, $argv) && '--complete' === $argv[1]) {
    $date = '2013-12-16';
    do {
        $date = date('Y-m-d', strtotime($date . ' + 1 day'));
        $logRowSet = $logModel->findAuditableLog($date);
        list($objCount, $userCount, $userSet) = processLog($logRowSet);
        $r = array();
        foreach ($userCount as $searchMode => $set) {
            $aux = count($set) > 0 ? $objCount[$searchMode] / count($set) : 0;
            $r[] = $aux;
        }
        $ratio = $r[0] > 0 ? $r[1] / $r[0] : $r[1];
        echo "comp: $date -- "  . $ratio . ' -- total: ' . count($logRowSet) . PHP_EOL;


    } while ($date != date('Y-m-d'));
}
