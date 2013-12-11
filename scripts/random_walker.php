<?php

$baseUrl = $argv[1];
$url = $baseUrl . '/artist/Coldplay';

while (1) {
    echo "visiting $url..." . PHP_EOL;
    $content = file_get_contents($url);
    $dom = new DOMDocument();
    $dom->loadHTML($content);
    $xpath = new DomXpath($dom);
    $links = $xpath->query('//*[@class="item-square playlist-square object-playlist"]');
    $i = rand(0, $links->length);
    echo $links->length . ' links found' . PHP_EOL;
    $a = $links->item($i)->getElementsByTagName('a');
    $a = $a->item(0);
    $url = $baseUrl . $a->getAttribute('href');
    echo "sleep..." . PHP_EOL;
    sleep(60);
}
