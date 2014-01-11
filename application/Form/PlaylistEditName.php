<?php

/**
 * Form_PlaylistEditName
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
class Form_PlaylistEditName extends DZend_Form
{
    public function init()
    {
        $this->_useBootstrap = false;
        $this->setAttrib('class', 'playlist-form-name');
        $this->setMethod('post');
        $this->setAction('/playlist/editname');
        $element = new Zend_Form_Element_Text('newname');
        $element->setRequired();
        $this->addElement($element);

        $element = new Zend_Form_Element_Hidden('name');
        $element->setRequired();
        $this->addElement($element);

        $element = new Zend_Form_Element_Submit('submit');
        $element->setRequired();
        $element->setLabel($this->_t('save'));
        $this->addElement($element);
    }
}
