<?php

class Form_IncBoardIntro extends DZend_Form
{
    public function init()
    {
        $this->setAttrib('class', 'form-horizontal');
        $this->setAttrib('id', 'incboard-intro');
        $element = new Zend_Form_Element_Text('num');
        $element->setRequired();
        $element->setLabel($this->_t('Number of Elements'));
        $this->addElement($element);

        $this->addSubmit($this->_t('Plot'));
    }
}
