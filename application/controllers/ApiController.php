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

    protected $_trackModel;
    protected $_playlistModel;
    protected $_artistModel;
    protected $_musicTitleModel;
    protected $_artistMusicTitleModel;
    protected $_musicTrackLinkModel;
    protected $_bondModel;

    public function init()
    {
        parent::init();
        $this->_trackModel = new Track();
        $this->_playlistModel = new Playlist();
        $this->_artistModel = new Artist();
        $this->_musicTitleModel = new MusicTitle();
        $this->_artistMusicTitleModel = new ArtistMusicTitle();
        $this->_musicTrackLinkModel = new MusicTrackLink();
        $this->_bondModel = new Bond();
    }
    /**
     * searchAction API search call.
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
            $limit = $this->_request->getParam('limit') ?
                $this->_request->getParam('limit') : 9;
            $offset = $this->_request->getParam('offset') ?
                $this->_request->getParam('offset') : 1;
            $cache = Zend_Registry::get('cache');
            $key = sha1($q . $limit . $offset);
            // if (($list = $cache->load($key)) === false) {
                $youtube = new Youtube();
                $complement = array();
                $artist = null;
                $musicTitle = null;
                $resultSet = $youtube->search($q, $limit, $offset);
                if (
                    ($artist = $this->_request->getParam('artist')) !== null &&
                    ($musicTitle = $this->_request->getParam('musicTitle'))
                        !== null) {
                    // TODO: add the tracks, artist, musicTitles, and
                    // relationships between them.


                    // insert tracks.
                    foreach ($resultSet as $result) {
                        $trackRow = $this->_trackModel->insert(
                            array(
                                'title' => $result->title,
                                'url' => $result->you2better,
                                'cover' => $result->pic,
                                'duration' => $result->duration
                                )
                            );

                        $artistMusicTitleId = $this->_artistMusicTitleModel->insert($artist, $musicTitle);
                        $musicTrackLinkId = $this->_musicTrackLinkModel->bond($artistMusicTitleId, $trackRow->id, $this->_bondModel->search);
                    }
                }

                $item = array();
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

    public function autocompleteAction()
    {
        $q = $this->_request->getParam('q');
        $list = array();
        if (null !== $q) {
            $lastfm = new Lastfm();
            $resultSet = $lastfm->search($q);
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
     * postDispatch Facilitates output using Json
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
