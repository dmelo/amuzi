<?php

class Lastfm
{
    private $_baseUrl = 'http://ws.audioscrobbler.com/2.0/';
    private $_key;
    private $_secret;

    protected function _request($args)
    {
        $args['api_key'] = $this->_key;
        foreach ($args as $key => $value)
            $final[] = $key . '='. urlencode($value);

        return file_get_contents($this->_baseUrl . '?' . implode('&', $final));
    }

    protected function _calcName($artist, $musicTitle)
    {
        return "${artist} - ${musicTitle}";
    }

    protected function _getCover($track)
    {
        $covers = $track->getElementsByTagName('image');
        return $covers->length > 0 ? $covers->item(0)->nodeValue : '';
    }

    protected function _processResponseSearch($track)
    {
        $artist = $track->getElementsByTagName('artist')
            ->item(0)
            ->nodeValue;
        $musicTitle = $track->getElementsByTagName('name')
            ->item(0)
            ->nodeValue;
        $name = $this->_calcName($artist, $musicTitle);
        $cover = $this->_getCover($track);
        return  new LastfmEntry($name, $cover, $artist, $musicTitle);
    }

    protected function _processResponseSimilar($track)
    {
        $artist = $track->getElementsByTagName('artist')
            ->item(0)
            ->getElementsByTagName('name')
            ->item(0)
            ->nodeValue;
        $musicTitle = $track->getElementsByTagName('name')
            ->item(0)
            ->nodeValue;
        $name = $this->_calcName($artist, $musicTitle);
        $cover = $this->_getCover($track);
        return new LastfmEntry($name, $cover, $artist, $musicTitle);
    }

    public function _exploreDOM($xml, $func, $limit)
    {
        $resultSet = array();
        $xmlDoc = new DOMDocument();
        $i = 0;
        if ('' !== $xml) {
            $xmlDoc->loadXML($xml);
            foreach ($xmlDoc->getElementsByTagName('track') as $track) {
                $resultSet[] = $this->$func($track);
                $i++;
                if ($i >= $limit)
                    break;
            }
        }

        return $resultSet;

    }

    public function __construct()
    {
        $config = new Zend_Config_Ini(
            '../application/configs/application.ini',
            'production'
        );

        $this->_key = $config->lastfm->key;
        $this->_secret = $config->lastfm->secret;
    }

    public function search($q, $limit = 10, $offset = 1)
    {
        $args = array(
            'method' => 'track.search',
            'track' => $q
            );

        $xml = $this->_request($args);
        return $this->_exploreDOM($xml, '_processResponseSearch', $limit);
    }

    public function getSimilar($artist, $music, $limit = 10, $offset = 1)
    {
        $resultSet = array();
        $args = array(
            'method' => 'track.getsimilar',
            'artist' => $artist,
            'track' => $music
            );

        $xml = $this->_request($args);
        return $this->_exploreDOM($xml, '_processResponseSimilar', $limit);
    }
}
