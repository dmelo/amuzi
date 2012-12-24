<?php

/**
 * ShareController
 *
 * @package
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
class ShareController extends DZend_Controller_Action
{
    public function indexAction()
    {
        $version = file_get_contents('../version.txt');

        if (($command = $this->_request->getParam('command')) !== NULL
            && ($param = $this->_request->getParam('param')) !== NULL) {
            $this->_helper->layout->disableLayout();
            $this->view->command = $command;
            if ('t' === $command) {
                $this->view->data = $this->_trackModel->findRowById($param);
                $this->view->url = '/index/index/#t' . $param;
            }
            $this->view->lightningPackerScript()->exchangeArray(array());
            $this->view->lightningPackerLink()->exchangeArray(array());

            $this->view->lightningPackerScript()->appendFile("/js/jquery.js?v=$version");
            $this->view->lightningPackerScript()->appendFile("/js/share.js?v=$version");
            $this->view->lightningPackerLink()->appendStylesheet("/css/share.css?v=$version");

        }
    }
}
