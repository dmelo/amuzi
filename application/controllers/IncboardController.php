<?php

/**
 * IncboardController
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
class IncboardController extends DZend_Controller_Action
{
    public function init()
    {
        $js = Zend_Registry::get('js');
        $js[] = 'incboard-intro.js';
        Zend_Registry::set('js', $js);
        parent::init();
    }

    public function indexAction()
    {

        $this->view->form = new Form_IncBoardIntro();
        if ($this->_request->getParam('num') !== null) {
            $this->view->num = $this->_request->getParam('num');
        }
    }
}
