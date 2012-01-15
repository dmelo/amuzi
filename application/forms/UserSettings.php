<?php

class Form_UserSettings extends Diogo_Form
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

        $this->addElement('Radio',
            'privacy',
            array(
                'label' => $this->_t('Playlist\'s default privacy policy'),
                'multiOptions' => array(
                    'public' => $this->_t('Public'),
                    'private' => $this->_t('Private')
                    )
                )
            );

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
