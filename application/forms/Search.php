<?php

class Form_Search extends Diogo_Form
{
    protected $_placeholder = 'E.g.: Rolling Stones ...';

    public function init()
    {
        $element = new Zend_Form_Element_Text('q');
        $element->setRequired();
        $element->setAttrib('placeholder', $this->_t($this->_placeholder));
        $element->setAttrib('class', 'search');
        $this->addElement($element);

        $element = new Zend_Form_Element_Submit('submit');
        $element->setLabel('Search');
        $element->setAttrib('class', 'search');
        $this->addElement($element);

        $this->setAction('/api/search');
        $this->setAttrib('id', 'search');
        $this->_useBootstrap = false;
    }
}
