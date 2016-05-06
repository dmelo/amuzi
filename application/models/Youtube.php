<?php

/**
 * Youtube
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
class Youtube extends DZend_Model
{
    private $_baseUrl = 'https://www.googleapis.com/youtube/v3/search?';
    private $_cache;

    public function __construct()
    {
        parent::__construct();
        $this->_cache = Cache::get('cache');
    }

    public function search($q, $limit = 9, $offset = 1, $complement = array())
    {
        $optParams = array(
            'q' => $q,
            'maxResults' => (int) $limit,
        );


        $key = sha1('Youtube::searchV3' . implode(',', $optParams));

        if (($json = $this->_cache->load($key)) === false) {
            $this->_logger->debug(
                "Youtube::search cache miss - " . print_r($optParams, true)
            );
            $config = Zend_Registry::get('config');
            $client = new Google_Client();
            $client->setApplicationName($config->youtube->name);
            $client->setDeveloperKey($config->youtube->key);

            $server = new Google_Service_YouTube($client);
            try {
                $results = $server->search->listSearch(
                    'id,snippet', $optParams
                );
                $json = json_encode($results->toSimpleObject()) . PHP_EOL;
                $this->_logger->debug('Youtube::search json: ' . $json);

                $this->_cache->save($json, $key);
            } catch (Exception $e) {
                $this->_logger->debug(
                    'Error searching on youtube: ' . $e->getMessage() . PHP_EOL
                    . $e->getTraceAsString()
                );
            }

        } else {
            $this->_logger->debug(
                "Youtube::search cache hit - " . print_r($optParams, true)
            );
        }

        $resultSet = array();
        $obj = json_decode($json);
        if (isset($obj->items)) {
            foreach ($obj->items as $item) {
                if (!isset($item->id->videoId)) {
                    continue;
                }
                $entry = array();
                $entry['fid'] = $item->id->videoId;
                $entry['title'] = $item->snippet->title;
                $entry['cover'] = $item->snippet->thumbnails->default->url;
                $entry['duration'] = -1;

                if (!empty($complement)) {
                    $entry['artist'] = $complement['artist'];
                    $entry['musicTitle'] = $complement['musicTitle'];
                }

                $resultSet[] = new YoutubeEntry($entry);
            }
        }

        $this->_logger->debug(
            'Youtube::search q: ' . $q . '. result: '
            . print_r($resultSet, true)
        );

        return $resultSet;
    }
}
