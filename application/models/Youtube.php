<?php

class Youtube
{
    private $baseUrl = 'https://gdata.youtube.com/feeds/api/videos?';

    public function search($q, $limit = 2, $offset = 1)
    {
        $args = array(
                'q=' . urlencode($q),
                'max-results=' . (int) $limit,
                'start-index=' . (int) $offset
                );

        $xml = file_get_contents($this->baseUrl . implode('&', $args));
        var_dump($xml);
        $xmlDoc = new DOMDocument();
        $xmlDoc->loadXML($xml);
        foreach($xmlDoc->getElementsByTagName('entry') as $node) {
            foreach($node->getElementsByTagName('id') as $id)
                $entry['id'] = $id->nodeValue;
            foreach($node->getElementsByTagName('title') as $title)
                $entry['title'] = $title->nodeValue;
            foreach($node->getElementsByTagName('content') as $content)
                $entry['content'] = $content->nodeValue;
            
            $resultSet[] = new YoutubeEntry($entry);
        }

        return $resultSet;
    }
}
