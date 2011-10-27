<?php

class Application_Form_Search extends Zend_Form
{

    public function init()
    {
        $element = new Zend_Form_Element_Text('q');
        $element->setRequired();
        $element->setAttrib('placeholder', 'E.g.: Rolling Stones ...');
        $this->addElement($element);

        $element = new Zend_Form_Element_Submit('submit');
        $element->setLabel('Search');
        $this->addElement($element);

        $this->setAction('/api/search');
        $this->setAttrib('id', 'search');
    }
}
