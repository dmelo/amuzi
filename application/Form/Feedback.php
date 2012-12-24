<?php

/**
 * Form_Feedback
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
class Form_Feedback extends DZend_Form
{
    public function init()
    {
        $element = new Zend_Form_Element_Text('subject');
        $element->setRequired();
        $element->setLabel($this->_t('Subject'));
        $this->addElement($element);

        $element = new Zend_Form_Element_Checkbox('anonymous');
        $element->setLabel($this->_t('Anonymous'));
        $this->addElement($element);

        $element = new Zend_Form_Element_Textarea('comment');
        $element->setRequired();
        $element->setAttrib('rows', 9);
        $element->setAttrib('cols', 80);
        $this->addElement($element);

        $element = new Zend_Form_Element_Submit('submit');
        $element->setLabel($this->_t('Send'));
        $this->addElement($element);

        $element = new Zend_Form_Element_Button('cancel');
        $element->setLabel($this->_t('Cancel'));
        $this->addElement($element);

        $this->setMethod('post');
        $this->setAction('/feedback/index');
        $this->setAttrib('class', 'form-horizontal');
    }
}
