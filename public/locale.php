<?php

$lang = $_GET['lang'];
$file = "../locale/$lang.php";

if (file_exists($file)) {
        $data = include($file);
} else {
        $data = array();
}

echo json_encode($data);
