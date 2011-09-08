<?php

class Youtube
{
    private $_baseUrl = 'https://gdata.youtube.com/feeds/api/videos?';

    public function search($q, $limit = 10, $offset = 1)
    {
        $args = array(
                'q=' . urlencode($q),
                'max-results=' . (int) $limit,
                'start-index=' . (int) $offset
                );

        $xml = file_get_contents($this->_baseUrl . implode('&', $args));
        $xml = str_replace(array('<media:', '</media:'),
            array('<mediaa', '</mediaa'), $xml);
        $xmlDoc = new DOMDocument();
        $xmlDoc->loadXML($xml);
        foreach ($xmlDoc->getElementsByTagName('entry') as $node) {
            $filter = '/http:\/\/gdata.youtube.com\/.*\//';
            foreach ($node->getElementsByTagName('id') as $id)
                $entry['id'] =preg_replace($filter, '',
                    $id->nodeValue);
            foreach ($node->getElementsByTagName('title') as $title)
                $entry['title'] = $title->nodeValue;
            foreach ($node->getElementsByTagName('content') as $content)
                $entry['content'] = $content->nodeValue;
            foreach ($node->getElementsByTagName('mediaathumbnail') as $pic) {
                $entry['pic'] = $pic->getAttribute('url');
            }

            $entry['you2better'] = Zend_Registry::get('domain');
            $entry['you2better'] .= '/api/' . $entry['id'] . '/' .
                $entry['title'] . '.mp3';

            $resultSet[] = new YoutubeEntry($entry);
        }

        return $resultSet;
    }
}
