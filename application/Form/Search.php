<?php

/**
 * Form_Search
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
class Form_Search extends DZend_Form
{
    protected $_placeholder = 'E.g.: Rolling Stones ...';

    public function init()
    {
        $this->setAttrib('class', 'search form-search');
        $element = new Zend_Form_Element_Text('q');
        $element->setRequired();
        $element->setAttrib('placeholder', $this->_t($this->_placeholder));
        $element->setAttrib('class', 'search search-query span4');
        $this->addElement($element);

        foreach (array('artist', 'musicTitle', 'type') as $item) {
            $this->addElement(new Zend_Form_Element_Hidden($item));
        }

        $element = new Zend_Form_Element_Submit('submit');
        $element->setLabel('Search');
        $element->setAttrib('class', 'search btn');
        $this->addElement($element);

        $this->setAction('/api/searchsimilar');
        $this->setAttrib('id', 'search');
        $this->_useBootstrap = false;

        $this->setDecorators(
            array(
                'FormElements',
                array(
                    'HtmlTag',
                    array('tag' => 'dl', 'class' => 'input-append')
                ),
                'Form'
            )
        );
    }
}
