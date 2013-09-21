<?php

/**
 * LastfmRequest
 *
 * @package Amuzi
 * @version 1.0
 * Amuzi - Online music
 * Copyright (C) 2010-2013  Diogo Oliveira de Melo
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
class LastfmRequest
{
    private $_baseUrl = 'http://ws.audioscrobbler.com/2.0/';
    private $_method;
    private $_args;
    private $_dirtyArgs;
    private $_final;
    private static $_key = null;
    private static $_secret = null;

    private function _addAttrib($attr, $required = true)
    {
        if (array_key_exists($attr, $this->_dirtyArgs) && is_string($attr)
            && !empty($attr) && !empty($this->_dirtyArgs[$attr])) {
            $this->_args[$attr] = $this->_dirtyArgs[$attr];
        } elseif ($required) {
            throw new LastfmException(
                'Could not find required attribute ' . $attr
                . ' in argument array ' . print_r($this->_dirtyArgs)
                . ' for method ' . $this->_method
            );
        }
    }

    private function _addRequired($attr)
    {
        $this->_addAttrib($attr);
    }

    private function _addOptional($attr)
    {
        $this->_addAttrib($attr, false);
    }

    private function _calcFinal()
    {
        if (empty($this->final)) {
            $this->_args['method'] = $this->_method;
            $this->_args['api_key'] = self::$_key;
            foreach ($this->_args as $key => $value) {
                $this->_final[] = urlencode($key) . '=' . urlencode($value);
            }
        }
    }

    public function __construct($method, array $args)
    {
        if (null === self::$_key) {
            $config = new Zend_Config_Ini(
                '../application/configs/application.ini',
                APPLICATION_ENV
            );

            self::$_key = $config->lastfm->key;
            self::$_secret = $config->lastfm->secret;
        }

        $this->_dirtyArgs = $args;
        $this->_final = array();
        if (is_string($method) && !empty($method)) {
            $this->_method = $method;
            $this->_args = array();
            switch ($method) {
                case 'track.search':
                    /*
                     * limit (Optional) : The number of results to fetch per
                     * page. Defaults to 30.
                     * page (Optional) : The page number to fetch. Defaults to
                     * first page.
                     * track (Required) : The track name
                     * artist (Optional) : Narrow your search by specifying an
                     * artist.
                     */
                    $this->_addOptional('limit');
                    $this->_addOptional('page');
                    $this->_addRequired('track');
                    $this->_addOptional('artist');
                    break;
                case 'track.getSimilar':
                    /*
                     * track (Required (unless mbid)] : The track name
                     * artist (Required (unless mbid)] : The artist name
                     * mbid (Optional) : The musicbrainz id for the track
                     * autocorrect[0|1] (Optional) : Transform misspelled...
                     * limit (Optional) : Maximum number of similar tracks to...
                     */
                    $this->_addRequired('track');
                    $this->_addRequired('artist');
                    $this->_addOptional('mbid');
                    $this->_addOptional('autocorrect');
                    $this->_addOptional('limit');
                    break;
                case 'album.search':
                    /*
                     * limit (Optional) : The number of results to fetch per
                     * page. Defaults to 30.
                     * page (Optional) : The page number to fetch. Defaults to
                     * first page.
                     * album (Required) : The album name
                     */
                    $this->_addOptional('limit');
                    $this->_addOptional('page');
                    $this->_addRequired('album');
                    break;
                case 'album.getInfo':
                    /*
                     * artist (Required (unless mbid)] : The artist name
                     * album (Required (unless mbid)] : The album name
                     * mbid (Optional) : The musicbrainz id for the album
                     * autocorrect[0|1] (Optional) : Transform misspelled...
                     * username (Optional) : The username for the context of...
                     * lang (Optional) : The language to return the biography...
                     */
                    $this->_addRequired('artist');
                    $this->_addRequired('album');
                    $this->_addOptional('mbid');
                    $this->_addOptional('autocorrect');
                    $this->_addOptional('username');
                    break;
                case 'artist.getInfo':
                    /*
                     * artist (Required (unless mbid)] : The artist name
                     * mbid (Optional) : The musicbrainz id for the artist
                     * lang (Optional) : The language to return the biography...
                     * autocorrect[0|1] (Optional) : Transform misspelled...
                     * username (Optional) : The username for the context of...
                     */
                    $this->_addRequired('artist');
                    $this->_addOptional('mbid');
                    $this->_addOptional('lang');
                    $this->_addOptional('autocorrect');
                    $this->_addOptional('username');
                    break;
                case 'artist.getTopAlbums':
                    /*
                     * artist (Required (unless mbid)] : The artist name
                     * mbid (Optional) : The musicbrainz id for the artist
                     * autocorrect[0|1] (Optional) : Transform misspelled...
                     * page (Optional) : The page number to fetch. Defaults to..
                     * limit (Optional) : The number of results to fetch per...
                     */
                    $this->_addRequired('artist');
                    $this->_addOptional('mbid');
                    $this->_addOptional('autocorrect');
                    $this->_addOptional('page');
                    $this->_addOptional('limit');
                    break;
                case 'geo.getTopTracks':
                    /*
                     *country (Required) : A country name, as defined by the...
                     location (Optional) : A metro name, to fetch the charts...
                     limit (Optional) : The number of results to fetch per...
                     page (Optional) : The page number to fetch. Defaults...
                     */
                    $this->_addRequired('country');
                    $this->_addOptional('location');
                    $this->_addOptional('limit');
                    $this->_addOptional('page');
                    break;
                default:
                    throw new LastfmException(
                        'Could not find method ' . $method
                    );
            }
        }
    }

    public function getKey()
    {
        empty($this->_final) && $this->_calcFinal();
        return sha1('Lastfm::_request' . implode($this->_final));
    }

    public function getUrl()
    {
        empty($this->_final) && $this->_calcFinal();
        return $this->_baseUrl . '?' . implode('&', $this->_final);
    }

    public function getArgs()
    {
        return $this->_args;
    }
}
