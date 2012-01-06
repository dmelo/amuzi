<?php

class Application_Form_UserSettings extends Diogo_Form
{
    public function init()
    {
        $element = new Zend_Form_Element_Text('Name');
        $element->setRequired();
        $element->setLabel($this->_t('Name'));
        $this->addElement($element);

        $element = new Zend_Form_Element_Text('Email');
        $element->setLabel($this->_t('Email'));
        $this->addElement($element);

        $this->addElement('Radio',
            'Privacy',
            array(
                'label' => $this->_t('Playlist\'s default privacy policy'),
                'multiOptions' => array(
                    'private' => $this->_t('Private'),
                    'public' => $this->_t('Public')
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
