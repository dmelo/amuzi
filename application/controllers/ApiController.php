<?php

/**
 * ApiController
 *
 * @version 1.0
 * @copyright Copyright (C) 2011 Diogo Oliveira de Melo. All rights reserved.
 * @author Diogo Oliveira de Melo <dmelo87@gmail.com>
 * @license GPL
 *
 */
class ApiController extends DZend_Controller_Action
{
    protected $_error = array(
            'error' => 'Parameter "q" must be specified'
            );

    protected function _registerTracks($resultSet, $artist, $musicTitle)
    {
        foreach ($resultSet as $result) {
            $data = array(
                'title' => $result->title,
                'fid' => $result->fid,
                'fcode' => $result->fcode,
                'cover' => $result->cover,
                'duration' => $result->duration
            );
            $trackRow = $this->_trackModel->insert($data);


            $artistMusicTitleId = $this->_artistMusicTitleModel
                ->insert($artist, $musicTitle);
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
                        array('artist' => $artist, 'musicTitle' => $musicTitle):
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
            }
            else
                $this->view->output = $this->_error;
        } catch(Exception $e) {
            $this->view->exception = $e;
        }
    }

    /**
     * searchsimilarAction API call that searches for similar artist/music and
     * compile a list of music objects.
     *
     * @return void
     */
    public function searchsimilarAction()
    {
        if (($artist = $this->_request->getParam('artist')) !== null &&
            ($musicTitle = $this->_request->getParam('musicTitle')) !== null) {
            $titlesLimit = $this->_request->getParam('titlesLimit', 2);
            $limit = $this->_request->getParam('limit', 9);
            $offset = $this->_request->getParam('offset', 1);
            $q = array();
            $list = array();
            $i = 0;

            $artistMusicTitleId = $this->_artistMusicTitleModel->insert(
                $artist, $musicTitle
            );

            foreach ($this->_lastfmModel->getSimilar($artist, $musicTitle) as
                $row) {
                $i++;
                if ($i <= $titlesLimit) {
                    $q[] = $row->name;
                    $resultSet = $this->_youtubeModel->search(
                        $row->name, $limit, $offset, array(
                            'artist' => $row->artist,
                            'musicTitle' => $row->musicTitle
                        )
                    );
                    $this->_registerTracks(
                        $resultSet, $row->artist, $row->musicTitle
                    );
                    $trackRow = $this->_musicTrackLinkModel->getTrack(
                        $row->artist, $row->musicTitle
                    );

                    $list[] = array_merge(
                        $trackRow->getArray(),
                        array(
                            'artist' => $row->artist,
                            'musicTitle' => $row->musicTitle
                        )
                    );
                }

                $artistMusicTitleId2 = $this->_artistMusicTitleModel->insert(
                    $row->artist, $row->musicTitle
                );

                $this->_musicSimilarityModel->insert(
                    $artistMusicTitleId, $artistMusicTitleId2, $row->similarity
                );
            }

            $this->view->output = $list;
        } else
            $this->view->output = $this->_error;
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
            $resultSet = $this->_lastfmModel->search($q);
            foreach ($resultSet as $result)
                $list[] = $result->getArray();
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
            if (null !== $track)
                $this->view->output = $track->toArray();
            else
                $this->view->output = null;
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
}
