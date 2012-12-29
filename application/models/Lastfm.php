<?php

/**
 * Lastfm
 *
 * @package Amuzi
 * @version 1.0
 * Amuzi - Online music
 * Copyright (C) 2010-2012  Diogo Oliveira de Melo
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
class Lastfm extends DZend_Model
{
    private $_baseUrl = 'http://ws.audioscrobbler.com/2.0/';
    private $_key;
    private $_secret;
    private $_cache;

    protected function _request($args)
    {
        $args['api_key'] = $this->_key;
        foreach ($args as $key => $value)
            $final[] = $key . '='. urlencode($value);

        $url = $this->_baseUrl . '?' . implode('&', $final);
        $this->_logger->debug('Lastfm::_request - ' . $url);

        return file_get_contents($url);
    }

    protected function _calcName($artist, $musicTitle)
    {
        return "${artist} - ${musicTitle}";
    }

    protected function _getCover($track)
    {
        $sizes = array('extralarge', 'large', 'medium', 'small');
        $currentSize = null;
        $cover = '';
        $covers = $track->getElementsByTagName('image');

        for ($i = 0; $i < $covers->length; $i++) {
            $size = $covers->item($i)->attributes->getNamedItem('size')->nodeValue;
            if (null === $currentSize
                || array_search($currentSize, $sizes) > array_search($size, $sizes)
            ) {
                $currentSize = $size;
                $cover = $covers->item($i)->nodeValue;
            }
        }

        return $cover;
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
        $entry = $this->_processResponseGetTop($track);
        $this->_logger->debug(
            'Lastfm::_processResponseSimilar 0 nodeValue'
            . $track->getElementsByTagName('match')->item(0)->nodeValue
        );
        $this->_logger->debug(
            'Lastfm::_processResponseSimilar 1 nodeValue'
            . (
                $track->getElementsByTagName('match')->item(0)->nodeValue
                * 10000.0
              )
        );
        $similarity = $track->getElementsByTagName('match')
            ->item(0)
            ->nodeValue * 10000.0;
        $this->_logger->debug(
            'Lastfm::_processResponseSimilar 2 nodeValue' . ((int)$similarity)
        );
        $entry->similarity = (int) $similarity;

        return $entry;
    }

    public function _processResponseGetTop($track)
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

    public function _exploreDOM($xml, $func, $limit = null)
    {
        $resultSet = array();
        $xmlDoc = new DOMDocument();
        $i = 0;
        if ('' !== $xml) {
            $xmlDoc->loadXML($xml);
            foreach ($xmlDoc->getElementsByTagName('track') as $track) {
                $resultSet[] = $this->$func($track);

                if (null !== $limit) {
                    $i++;
                    if ($i >= $limit)
                        break;
                }
            }
        }

        return $resultSet;

    }

    public function __construct()
    {
        parent::__construct();
        $config = new Zend_Config_Ini(
            '../application/configs/application.ini',
            'production'
        );

        $this->_key = $config->lastfm->key;
        $this->_secret = $config->lastfm->secret;
        $this->_cache = Zend_Registry::get('cache');
    }

    public function search($q, $limit = 10, $offset = 1)
    {
        $key = sha1("Lastfm::search#$q");

        $this->_logger->debug('Lastfm::search A ' . microtime(true));
        if (($xml = $this->_cache->load($key)) === false) {
            $args = array(
                'method' => 'track.search',
                'track' => $q
                );

            $xml = $this->_request($args);
            $this->_cache->save($xml, $key);
            $this->_logger->debug('Lastfm::search B ' . microtime(true));
        }
        $this->_logger->debug('Lastfm::search C ' . microtime(true));

        return $this->_exploreDOM($xml, '_processResponseSearch', $limit);
    }

    public function getSimilar($artist, $music)
    {
        $key = sha1("Lastfm::search#$artist#$music");

        $this->_logger->debug('Lastfm::getSimilar A ' . microtime(true));
        if (($xml = $this->_cache->load($key)) === false) {
            $resultSet = array();
            $args = array(
                'method' => 'track.getsimilar',
                'artist' => $artist,
                'track' => $music
                );

            $xml = $this->_request($args);
            $this->_cache->save($xml, $key);
            $this->_logger->debug('Lastfm::getSimilar B ' . microtime(true));
        }
        $this->_logger->debug('Lastfm::getSimilar C ' . microtime(true));

        return $this->_exploreDOM($xml, '_processResponseSimilar', 200);
    }

    public function getTop($limit = 50)
    {
        $date = date('Ymd');
        $key = sha1("Lastfm::getTop#$limit#$date");

        if (($xml = $this->_cache->load($key)) === false) {
            $resultSet = array();
            $args = array(
                'method' => 'geo.gettoptracks',
                'country' => 'united states'
            );

            $xml = $this->_request($args);
            $this->_logger->debug("Lastfm::getTop -> xml: " . $xml);
            $this->_cache->save($xml, $key);
        }

        return $this->_exploreDOM($xml, '_processResponseGetTop', $limit);
    }
}
