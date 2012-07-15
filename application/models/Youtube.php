<?php

class Youtube
{
    private $_baseUrl = 'https://gdata.youtube.com/feeds/api/videos?';

    public function search($q, $limit = 9, $offset = 1, $complement = array())
    {
        $args = array(
                'q=' . urlencode($q),
                'max-results=' . (int) $limit,
                'start-index=' . (int) $offset
                );

        $xml = file_get_contents($this->_baseUrl . implode('&', $args));
        $xml = str_replace(
            array('<media:', '</media:'), array('<mediaa', '</mediaa'), $xml
        );

        $xmlDoc = new DOMDocument();

        $xmlDoc->loadXML($xml);
        $resultSet = array();
        foreach ($xmlDoc->getElementsByTagName('entry') as $node) {
            $filter = '/http:\/\/gdata.youtube.com\/.*\//';
            foreach ($node->getElementsByTagName('id') as $id)
                $entry['id'] = preg_replace(
                    $filter, '', $id->nodeValue
                );
            foreach ($node->getElementsByTagName('title') as $title)
                $entry['title'] = $title->nodeValue;
            // filtering
            $entry['title'] = str_replace(array('"', '\'', '/'), array('', '', ''), strip_tags($entry['title']));

            foreach ($node->getElementsByTagName('content') as $content)
                $entry['content'] = $content->nodeValue;

            foreach ($node->getElementsByTagName('mediaathumbnail') as $cover)
                $entry['cover'] = $cover->getAttribute('url');

            $mediaContentList = $node->getElementsByTagName('mediaacontent');
            foreach ($mediaContentList as $mediaContent)
                $entry['duration'] = $mediaContent->getAttribute('duration');

            if(!array_key_exists('duration', $entry))
                continue;

            $entry['url'] = Zend_Registry::get('domain');
            $entry['url'] .= '/api/' . $entry['duration'] . '/' .
                $entry['id'] . '/' . urlencode($entry['title']) . '.mp3';


            foreach ($node->getElementsByTagName('link') as $link) {
                $href = $link->getAttribute('href');
                if (strpos($href, "youtube") !== false && strpos($href, "watch") !== false)
                    $entry['youtubeUrl'] = $href;
            }

            if(!empty($complement)) {
                $entry['artist'] = $complement['artist'];
                $entry['musicTitle'] = $complement['musicTitle'];
            }

            $resultSet[] = new YoutubeEntry($entry);
        }

        return $resultSet;
    }
}
