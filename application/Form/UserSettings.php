<?php

/**
 * Form_UserSettings
 *
 * @package Amuzi
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
class Form_UserSettings extends DZend_Form
{
    public function init()
    {
        $element = new Zend_Form_Element_Text('name');
        $element->setRequired();
        $element->setLabel($this->_t('Name'));
        $this->addElement($element);

        $element = new Zend_Form_Element_Text('email');
        $element->setLabel($this->_t('Email'));
        $this->addElement($element);

        $this->addElement(
            'Radio',
            'privacy',
            array(
                'label' => $this->_t('Playlist\'s default privacy policy'),
                'multiOptions' => array(
                    'public' => $this->_t('Public'),
                    'private' => $this->_t('Private')
                    )
            )
        );

        $element = new Zend_Form_Element_Select(
            'view', array(
                'label' => 'View',
                'required' => true
            )
        );

        $element->addMultiOptions(
            array(
                'default' => 'Classic View',
                'incboard' => 'IncBoard'
            )
        );

        $this->addElement($element);

        $element = new Zend_Form_Element_Submit('submit');
        $element->setLabel($this->_t('Save'));
        $this->addElement($element);

        $element = new Zend_Form_Element_Button('cancel');
        $element->setLabel($this->_t('Cancel'));
        $this->addElement($element);

        $this->setAction('/user/index');
        $this->setAttrib('id', 'usersettings');

    }
}
