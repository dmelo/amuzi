<?php

/**
 * Form_CreatePlaylist
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
class Form_CreatePlaylist extends DZend_Form
{
    public function init()
    {
        $element = new Zend_Form_Element_Text('name');
        $element->setRequired();
        $element->setLabel($this->_t('Name'));
        $this->addElement($element);

        $element = new Zend_Form_Element_Checkbox('public');
        $element->setRequired();
        $element->setLabel($this->_t('Public'));
        $session = DZend_Session_Namespace::get('session');
        if(isset($session->user) && isset($session->user->privacy))
            $element->setChecked('public' === $session->user->privacy);
        else
            $element->setChecked(true);

        $this->addElement($element);

        $element = new Zend_Form_Element_Submit('submit');
        $element->setLabel($this->_t('Create'));
        $this->addElement($element);


        $element = new Zend_Form_Element_Button('cancel');
        $element->setLabel($this->_t('Cancel'));
        $this->addElement($element);

        $this->setAction('/playlist/new');
        $this->setAttrib('id', 'newPlaylist');
        $this->setMethod('GET');
    }
}
