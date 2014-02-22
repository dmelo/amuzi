<?php

/**
 * Form_UserSettings
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
class Form_UserSettings extends DZend_Form
{
    public function init()
    {
        $element = new Zend_Form_Element_Text('name');
        $element->setRequired();
        $element->setLabel($this->_t('Name'));
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
                'label' => $this->_t('View'),
                'required' => true
            )
        );

        $element->addMultiOptions(
            array(
                'default' => $this->_t('Classic View'),
                'incboard' => $this->_t('IncBoard')
            )
        );

        $this->addElement($element);

        $element = new Zend_Form_Element_Select(
            'lang', array(
                'label' => $this->_t('Language'),
                'required' => 'true'
            )
        );

        $element->addMultiOptions(
            array(
                'en_US' => $this->_t('English'),
                'pt_BR' => $this->_t('Portuguese (incomplete)'),
                'es'    => $this->_t('Spanish (incomplete)')
            )
        );
        $this->addElement($element);

        $this->addSubmit($this->_t('Save'));

        $element = new Zend_Form_Element_Button('cancel');
        $element->setLabel($this->_t('Cancel'));
        $this->addElement($element);

        $element = new Zend_Form_Element_Hidden('windowId');
        $this->addElement($element);

        $this->setAction('/user/index');
        $this->setAttrib('id', 'usersettings');

    }
}
