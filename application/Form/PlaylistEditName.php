<?php

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
