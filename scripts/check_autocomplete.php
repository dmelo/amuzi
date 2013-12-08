<?php

require_once '../scripts/lib.php';

$r = file_get_contents('http://amuzi.me/autocomplete.php?q=coldpl');
$data = json_decode($r);
$logger = Zend_Registry::get('logger');

if (count($data) === 10) {
    $logger->info('Autocomplete OK');
} else {
    $logger->err('Autocomplete ERROR');
    $mail = new DZend_Mail('UTF-8');
    $mail->setBodyText('Autocomplete is not working');
    $mail->setFrom('support@amuzi.net', 'Diogo Oliveira de Melo');
    $mail->addTo('dmelo87@gmail.com', 'Diogo Oliveira de Melo');
    $mail->send();
}

