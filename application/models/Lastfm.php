<?php

/**
 * Lastfm
 *
 * @package Amuzi
 * @version 1.0
 * Amuzi - Online music
 * Copyright (C) 2010-2014  Diogo Oliveira de Melo
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
    private $_cache;

    /**
     * _request Perform a request to lastfm.
     *
     * @param LastfmRequest $request;
     * @param bool $useCache
     * @return string xml
     */
    protected function _request(LastfmRequest $request, $useCache = true)
    {
        $c = new DZend_Chronometer();
        $c->start();
        if (($xml = $this->_cache->load($request->getKey())) === false) {
            $this->_logger->debug('Lastfm::_request - ' . $request->getUrl());
            $xml = @file_get_contents($request->getUrl());
            if ($useCache) {
                $this->_cache->save($xml, $request->getKey());
            }
        }

        $c->stop();
        $this->_logger->debug(
            'Lastfm::_request ' . print_r($request->getArgs(), true)
            . '. useCache: ' . $useCache . '. ' . $c->get()
        );

        return $xml;
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
            $size = $covers->item($i)->attributes->getNamedItem('size')
                ->nodeValue;
            if (null === $currentSize
                || array_search($currentSize, $sizes)
                    > array_search($size, $sizes)
            ) {
                $currentSize = $size;
                $cover = $covers->item($i)->nodeValue;
            }
        }

        return $cover;
    }

    protected function _processResponseSearch($track)
    {
        $artist = $track->getElementsByTagName('artist')->item(0);
        $name = $artist->getElementsByTagName('name');
        $this->_logger->debug(
            'Lastfm::_processResponseSearch -- ' . get_class($name)
        );

        if (get_class($name) === 'DOMNodeList' && $name->item(0) !== null) {
            $artist = $name->item(0)->nodeValue;
        } else {
            $artist = $artist->nodeValue;
        }

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

        $this->_logger->debug(
            "Lastfm::_processResponseGetTop $artist $musicTitle $cover --> "
            . $track->nodeValue
        );

        return new LastfmEntry($name, $cover, $artist, $musicTitle);
    }

    public function _exploreDOM($xml, $func, $limit = null)
    {
        $c = new DZend_Chronometer();
        $c->start();
        $type = 'track';
        $resultSet = array();
        $xmlDoc = new DOMDocument();
        $i = 0;
        if ('' !== $xml) {
            file_put_contents("tmp/gettop.xml", $xml);
            $this->_logger->debug("Lastfm::_exploreDOM xml -- $xml");
            $xmlDoc->loadXML($xml);
            if ($xmlDoc->getElementsByTagName('track')->length === 0) {
                $type = 'album';
            }

            foreach ($xmlDoc->getElementsByTagName($type) as $track) {
                $item = $this->$func($track);
                $item->type = $type;
                $resultSet[] = $item;

                if (null !== $limit && $i++ >= $limit) {
                    break;
                }
            }
        }
        $c->stop();

        return $resultSet;
    }

    public function __construct()
    {
        parent::__construct();
        $this->_cache = Cache::get('cache');
    }

    public function searchTrack($q, $limit = 5, $offset = 1)
    {
        $q = strtoupper($q);
        $xmlTrack = $this->_request(
            new LastfmRequest(
                'track.search',
                array('track' => $q)
            )
        );

        $list = $this->_exploreDOM($xmlTrack, '_processResponseSearch', $limit);
        $ret = array();
        foreach ($list as $item) {
            $ret[] = new AutocompleteEntry(
                $item->artist,
                $item->musicTitle,
                $item->cover,
                'track'
            );
        }

        return array_slice($ret, 0, 5);
    }

    public function searchAlbum($q, $limit = 5, $offset = 1)
    {
        $q = strtoupper($q);
        $xmlAlbum = $this->_request(
            new LastfmRequest(
                'album.search',
                array('album' => $q)
            )
        );

        $list = $this->_exploreDOM($xmlAlbum, '_processResponseSearch', $limit);
        $ret = array();
        foreach ($list as $item) {
            $ret[] = array(
                'artist' => $item->artist,
                'name' => $item->musicTitle,
                'cover' => $item->cover,
                'type' => 'album'
            );
        }

        return $ret;
    }

    public function getAlbum($artist, $album)
    {
        $ret = null;
        $xml = $this->_request(
            new LastfmRequest(
                'album.getInfo',
                array(
                    'album' => $album,
                    'artist' => $artist,
                    'autocorrect' => 0,
                )
            )
        );

        $albumName = $artist = $cover = '';
        $xmlDoc = new DOMDocument();
        if (!empty($xml)) {
            $xmlDoc->loadXML($xml);
            $this->_logger->debug("XML: " . $xml);
            $album = $xmlDoc->getElementsByTagName('album');
            for ($e = $album->item(0)->firstChild; null !== $e;
                $e = $e->nextSibling) {
                $this->_logger->debug(
                    'Lastfm::getAlbum nodeName: '
                    . $e->nodeName . '. nodeValue: ' . $e->nodeValue
                );
                $value = $e->nodeValue;
                switch ($e->nodeName) {
                    case 'name':
                        $albumName = $value;
                        break;
                    case 'artist':
                        $artist = $value;
                        break;
                    case 'image':
                        $cover = $value;
                        break;
                }
            }

            $trackList = $this->_exploreDOM(
                $xml, '_processResponseSearch', 1000
            );

            $ret = new LastfmAlbum($albumName, $cover, $artist, $trackList);
        }

        $this->_logger->debug('Lastfm::getAlbum - ' . $ret);

        return $ret;
    }

    public function getArtist($name)
    {
        $xml = $this->_request(
            new LastfmRequest(
                'artist.getInfo',
                array('artist' => $name)
            )
        );
        $this->_logger->debug('Lastfm::getArtist xml ' . $xml);

        $xmlDoc = new DOMDocument();
        $cover = null;
        $info = null;
        $similarityList = array();
        if (!empty($xml)) {
            $xmlDoc->loadXML($xml);
            $artist = $xmlDoc->getElementsByTagName('artist');
            for ($e = $artist->item(0)->firstChild; null !== $e;
                $e = $e->nextSibling) {
                $value = $e->nodeValue;
                switch ($e->nodeName) {
                    case 'bio':
                        $info = $value;
                        break;
                    case 'image':
                        $cover = $value;
                        break;
                    case 'similar':
                        for (
                            $similar = $e->firstChild; null !== $similar;
                            $similar = $similar->nextSibling
                        ) {
                            $item = array();
                            if ('artist' === $similar->nodeName) {
                                for (
                                    $child = $similar->firstChild;
                                    null !== $child;
                                    $child = $child->nextSibling
                                ) {
                                    if ('name' === $child->nodeName) {
                                        $item['name'] = $child->nodeValue;
                                    } elseif ('image' === $child->nodeName) {
                                        $item['cover'] = $child->nodeValue;
                                    }
                                }
                                $similarityList[] = $item;
                            }
                        }
                        break;
                }
            }
        }

        return array(
            'cover' => $cover,
            'info' => $info,
            'similarityList' => $similarityList
        );
    }

    public function getArtistTopAlbum($artistName)
    {
        $xml = $this->_request(
            new LastfmRequest(
                'artist.getTopAlbums',
                array(
                    'artist' => $artistName,
                    'limit' => 10
                )
            )
        );
        $this->_logger->debug('Lastfm::getArtistTopAlbum xml ' . $xml);

        $xmlDoc = new DOMDocument();
        $cover = null;
        $info = null;
        $similarityList = array();
        $ret = array();
        if ('' !== $xml) {
            $xmlDoc->loadXML($xml);
            $albumList = $xmlDoc->getElementsByTagName('album');
            for ($i = 0; $i < $albumList->length; $i++) {
                $album = $albumList->item($i);
                $item = array();
                for (
                    $e = $album->firstChild; null != $e; $e = $e->nextSibling
                ) {
                    switch ($e->nodeName) {
                        case 'name':
                            $item['name'] = $e->nodeValue;
                            break;
                        case 'artist':
                            for (
                                $artist = $e->firstChild; null != $artist;
                                $artist = $artist->nextSibling
                             ) {
                                if ('name' === $artist->nodeName) {
                                    $item['artist'] = $artist->nodeValue;
                                    break;
                                }
                            }
                            break;
                        case 'image':
                            $item['cover'] = $e->nodeValue;
                            break;
                    }
                }
                $ret[] = $item;
            }
        }

        return $ret;

    }


    public function getSimilar($artist, $music)
    {
        $this->_logger->debug(
            'Lastfm::getSimilar A ' . $artist . ' - ' . $music
            . ' # ' . microtime(true)
        );
        $resultSet = array();
        $xml = $this->_request(
            new LastfmRequest(
                'track.getSimilar',
                array(
                    'artist' => $artist,
                    'track' => $music
                )
            )
        );
        $this->_logger->debug('Lastfm::getSimilar B ' . microtime(true));

        return $this->_exploreDOM($xml, '_processResponseSimilar', 200);
    }

    public function getTop($limit = 50)
    {
        $date = date('Ymd', time(null));
        $c = new DZend_Chronometer();
        $key = sha1("Lastfm::getTop#$limit#$date#a");
        $this->_logger->debug('ApiController::gettop 4 - ' . $key);

        if (($xml = $this->_cache->load($key)) === false) {
            $c = new DZend_Chronometer();
            $c->start();
            $resultSet = array();
            $xml = $this->_request(
                new LastfmRequest(
                    'geo.getTopTracks',
                    array('country' => 'united states')
                ), false
            );
            $this->_logger->debug("Lastfm::getTop -> xml: " . $xml);
            $this->_cache->save($xml, $key);
            $c->stop();
            $this->_logger->debug('ApiController::gettop 3 - ' . $c->get());
        }

        return $this->_exploreDOM($xml, '_processResponseGetTop', $limit);
    }

}
