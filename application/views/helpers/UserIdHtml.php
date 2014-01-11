<?php

/**
 * View_Helper_StatusMessage
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
class View_Helper_UserIdHtml extends Zend_View_Helper_Abstract
{
    public $view;

    public function userIdHtml()
    {
        $ret = '';
        if (isset($this->view->user)) {
            $data = array(
                'id' => 'userId',
                'email' => 'email'
            );

            foreach ($data as $key => $value) {
                $ret .= "<div class=\"invisible\" id=\"$value\">"
                . $this->view->user->$key . "</div>";
            }
        }

        return $ret;
    }

    public function setView(Zend_View_Interface $view)
    {
        $this->view = $view;
    }
}
