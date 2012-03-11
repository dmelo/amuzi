<?php

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
