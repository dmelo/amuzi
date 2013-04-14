<?php

/**
 * App_Plugin_Login
 *
 * @package Amuzi
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
class App_Plugin_Login extends DZend_Plugin_Login
{
    public function __construct()
    {
        parent::__construct();
        $this->_allowLogOutAccess = array(
            array('default', 'api'),
            array('default', 'docs'),
            array('default', 'share'),
            array('default', 'index', 'about'),
            array('default', 'index', 'help'),
            array('default', 'index', 'error'),
            array('default', 'index', 'test'),
            array('default', 'index', 'login')
        );
    }
}
