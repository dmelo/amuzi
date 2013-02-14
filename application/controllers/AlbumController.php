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
