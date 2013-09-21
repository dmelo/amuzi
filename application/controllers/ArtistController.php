<?php

class ArtistController extends DZend_Controller_Action
{
    public function infoAction()
    {
        $artistRow = null;

        if (($id = $this->_request->getParam('id')) !== null) {
            $artistRow = $this->_artistModel->findRowById($id);
        } elseif (($name = $this->_request->getParam('name')) !== null) {
            $artistRow = $this->_artistModel->findRowByName($name);
            if (null === $artistRow) {
                $info = $this->_lastfmModel->getArtist($name);
                if (null !== $info['info']) {
                    $this->_artistModel->insert($name);
                    $artistRow = $this->_artistModel->findRowByName($name);
                }
            }
        }

        if (null !== $artistRow) {
            $this->view->artistRow = $artistRow;
        }
    }
}
