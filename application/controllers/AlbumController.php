<?php

class AlbumController extends DZend_Controller_Action
{
    public function init()
    {
        parent::init();
        $this->_jsonify = true;
    }

    public function addAction()
    {
        if (($albumId = $this->_request->getParam('albumId')) !== false) {
            $this->view->output = $this->
                _userListenAlbumModel->insert($albumId);
            $albumRow = $this->_albumModel->findRowById($albumId);
            $albumRow->getTrackListSync();
            foreach ($albumRow->trackList as $track) {
                if (2 === count($track)) {
                    $artist = $track['artist'];
                    $musicTitle = $track['musicTitle'];
                    $this->_musicTrackLinkModel->getTrackSync($artist, $musicTitle);
                    // I don't have to store the value because it doesn't
                    // return the album.
                }
            }
        }
    }

    public function loadAction()
    {
        if (($id = $this->_request->getParam('id')) !== false) {
            $albumRow = $this->_albumModel->findRowById($id);
            $album = $albumRow->getArray();
            $ret = array($album['trackList'], $album['name'], 0, 0, 0);

            $this->view->output = $ret;
        }
    }

    public function infoAction()
    {
        if (($id = $this->_request->getParam('id')) !== false) {
            $collection = $this->view->collection = $this->_albumModel->findRowById($id);
            $c = new DZend_Chronometer();

            $c->start();
            $collection->getCoverName();
            $c->stop();
            $this->_logger->debug("AlbumController::info covername " . $c->get());

            foreach (array(
                'getCoverName', 'getType', 'playTime', 'getTrackListAsArray'
                ) as $f) {
                $c->start();
                $collection->$f();
                $c->stop();
                $this->_logger->debug("AlbumController::info $f " . $c->get());
            }


            $this->renderScript('playlist/info.phtml');
        }
    }

    public function listAction()
    {
        $this->view->playlistRowSet = $this->_albumModel->findAllFromUser();
        $this->renderScript('playlist/list.phtml');
    }

    public function removeAction()
    {
        $message = array();

        if ($this->_request->isPost() &&
            ($id = $this->_request->getPost('id')) !== null) {
            if (($msg = $this->_albumModel = $this->_albumModel->remove($id)) === true) {
                $message = array($this->view->t('Album removed'), 'success');
            } else {
                $message = array(
                    $this->view->t('Could not remove') . ': ' . $msg, 'error'
                );
            }
        }

        $this->view->output = $message;
    }
}
