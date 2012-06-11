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
        $resultSet = array();
        $args = array(
            'method' => 'track.search',
            'track' => $q
            );

        $xml = $this->_request($args);
        $xmlDoc = new DOMDocument();
        $i = 0;
        if ('' !== $xml) {
            $xmlDoc->loadXML($xml);
            foreach ($xmlDoc->getElementsByTagName('track') as $track) {
                $artists = $track->getElementsByTagName('artist');
                $names = $track->getElementsByTagName('name');
                $pics = $track->getElementsByTagName('image');
                $name = $artists->item(0)->nodeValue . ' - ';
                $name .= $names->item(0)->nodeValue;
                $pic = $pics->length > 0 ? $pics->item(0)->nodeValue : '';
                $resultSet[] = new LastfmEntry($name, $pic);
                $i++;
                if($i >= $limit)
                    break;
            }
        }

        return $resultSet;
    }
}
