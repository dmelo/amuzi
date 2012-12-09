<?php

/**
 * ApiController
 *
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
class ApiController extends DZend_Controller_Action
{
    protected $_error = array(
            'error' => 'Parameter "q" must be specified'
            );

    /**
     * _registerTracks Persists track on database.
     *
     * @param array $resultSet List of results to persist.
     * @param mixed $artist Artist related to the results' list.
     * @param mixed $musicTitle Music title related to the results' list.
     * @return void Return an array with the list of registered elements.
     */
    protected function _registerTracks(array $resultSet, $artist, $musicTitle)
    {
        $artistMusicTitleId = $this->_artistMusicTitleModel
            ->insert($artist, $musicTitle);
        $ret = array();

        foreach ($resultSet as $result) {
            $data = array(
                'title' => $result->title,
                'fid' => $result->fid,
                'fcode' => $result->fcode,
                'cover' => $result->cover,
                'duration' => $result->duration
            );
            $trackRow = $this->_trackModel->insert($data);

            $this->_musicTrackLinkModel->bond(
                $artistMusicTitleId,
                $trackRow->id,
                $this->_bondModel->search
            );

            $row = $trackRow->getArray();
            $row['artist'] = $artist;
            $row['musicTitle'] = $musicTitle;

            $ret[] = $row;
        }

        return $ret;
    }

    /**
     * searchAction API search call that outputs a list of track objects.
     *
     * @return void
     *
     */
    public function searchAction()
    {
        try {
            $q = $this->_request->getParam('q');
            $list = array();
            if (null !== $q) {
                $limit = $this->_request->getParam('limit', 9);
                $offset = $this->_request->getParam('offset', 1);
                $cache = Zend_Registry::get('cache');
                $key = sha1($q . $limit . $offset);
                // TODO: UNCOMMENT CACHE.
                // if (($list = $cache->load($key)) === false) {
                    $complement = array();
                    $artist = $this->_request->getParam('artist');
                    $musicTitle = $this->_request->getParam('musicTitle');
                    $complement = null !== $artist && null !== $musicTitle ?
                        array(
                            'artist' => $artist,
                            'musicTitle' => $musicTitle,
                            'artistMusicTitleId' => $this->
                                _artistMusicTitleModel->insert(
                                    $artist, $musicTitle
                                )
                        ):
                        array();
                    $resultSet = $this->_youtubeModel->search(
                        $q, $limit, $offset, $complement
                    );
                    if (!empty($complement))
                        $list = $this->_registerTracks(
                            $resultSet, $artist, $musicTitle
                        );
                    else
                        foreach ($resultSet as $result)
                            $list[] = $result->getArray();
                //    $cache->save($list, $key);
                //}

                $this->view->output = $list;
            } else {
                $this->view->output = $this->_error;
            }
        } catch(Exception $e) {
            $this->view->exception = $e;
        }
    }

    /**
     * searchsimilarAction API call that searches for similar artist/music and
     * compile a list of music objects.
     *
     * It will output an json array with two elements. The first is the list of 
     * all similar artist/musicTitle and the second is a matrix with the
     * similarity between each of it.
     *;
     * @return void
     */
    public function searchsimilarAction()
    {
        $artistMusicTitleIdList = $this->_request->getParam(
            'artistMusicTitleIdList', array()
        );

        if (($artist = $this->_request->getParam('artist')) != null &&
            ($musicTitle = $this->_request->getParam('musicTitle')) != null) {

            $this->view->output = $this->_musicSimilarityModel
                ->getSimilar($artist, $musicTitle, $artistMusicTitleIdList);
        } elseif (($q = $this->_request->getParam('q')) != null) {
            $item = $this->_artistMusicTitleModel->getBestGuess($q);
            $this->view->output = null === $item ?
                $this->_error :
                $this->_musicSimilarityModel->getSimilar(
                    $item['artist'], $item['musicTitle'], $artistMusicTitleIdList
                );
        } else {
            $this->view->output = $this->_error;
        }
    }

    /**
     * searchmusicAction Given the artist and musicTitle parameters, find all
     * necessary information of the given music.
     *
     * @return void
     */
    public function searchmusicAction()
    {
        if (($artist = $this->_request->getParam('artist')) !== null &&
            ($musicTitle = $this->_request->getParam('musicTitle')) !== null) {
            $trackRow = $this->_musicTrackLinkModel->getTrack(
                $artist, $musicTitle
            );
            if (null === $trackRow) {
                // Look for it on Youtube.
                $resultSet = $this->_youtubeModel->search(
                    "${artist} - ${musicTitle}", 5, 1, array(
                        'artist' => $artist,
                        'musicTitle' => $musicTitle
                    )
                );
                $this->_registerTracks(
                    $resultSet, $artist, $musicTitle
                );
                $trackRow = $this->_musicTrackLinkModel->getTrack(
                    $artist, $musicTitle
                );
            }

            if (null === $trackRow) {
                $this->view->output = null;
            } else {
                $this->view->output = array_merge(
                    $trackRow->getArray(),
                    array(
                        'artist' => $artist,
                        'musicTitle' => $musicTitle,
                        'artistMusicTitleId' => $this->_artistMusicTitleModel
                            ->insert($artist, $musicTitle)
                    )
                );
            }
        }
    }

    /**
     * Given the user's incomplete outputs the list of suggestions in Json.
     *
     * @return void
     */
    public function autocompleteAction()
    {
        $q = $this->_request->getParam('q');
        $list = array();
        if (null !== $q) {
            $list = $this->_artistMusicTitleModel->autocomplete($q);
            if (count($list) < 5) {
                $resultSet = $this->_lastfmModel->search($q);
                foreach ($resultSet as $result)
                    $list[] = $result->getArray();
            }
            $this->view->output = $list;
        }
        else
            $this->view->output = $this->_error;
    }

    /**
     * Given the track id, return information about a specific song.
     *
     * @return void
     */
    public function gettrackAction()
    {
        if (($id = $this->_request->getParam('id')) !== null) {
            $track = $this->_trackModel->findRowById($id);
            if (null !== $track) {
                $this->view->output = $track->toArray();
            } else {
                $this->view->output = null;
            }
        }
    }

    /**
     * Given the playlist id, return information about a specific playlist.
     *
     * @return void
     */
    public function getplaylistAction()
    {
        if (($id = $this->_request->getParam('id')) !== null) {
            $playlist = $this->_playlistModel->findRowById($id);
            // Only return the playlist if it's owned but the user or it is
            // public.
        }
    }

    public function tracksettingsAction()
    {
        $trackId = $this->_request->getParam('track_id');
        $artistMusicTitleId = $this->_request->getParam(
            'artist_music_title_id'
        );
        $trackRow = $this->_trackModel->findRowById($trackId);

        $this->view->trackTitle = $trackRow->title;
        $this->view->trackId = $trackId;
        $this->view->artistMusicTitleId = $artistMusicTitleId;
        $this->view->mp3 = $trackRow->mp3;
        $this->view->flv = $trackRow->flv;
        $this->view->youtubeUrl = $trackRow->youtubeUrl;
    }

    /**
     * postDispatch Make it easier to output Json.
     *
     * @return void
     *
     */
    public function postDispatch()
    {
        if (isset( $this->view->output ))
            echo Zend_Json::encode($this->view->output);
    }

    public function testAction()
    {
        $this->_taskRequestModel->addTask('SearchSimilar', 'Coldplay', 'Bla');
    }

    /**
     * reporterrorAction In case of JS error, the default procedure must be
     * call this action to report it.
     *
     * @return void
     */
    public function reporterrorAction()
    {
        $message = array('Wrong parameters/method', 'error');
        if ($this->_request->isPost()) {
            $origin = $this->_request->getParam('origin');
            $err = $this->_request->getParam('err');
            $obj = $this->_request->getParam('obj');
            $this->_logger->err(
                "ApiController::reporterrorAction -> origin: $origin. "
                . "err: $err. obj: $obj"
            );
            $message = array('Error reported successfully', 'success');
        }

        $this->view->message = $message;
    }
}
