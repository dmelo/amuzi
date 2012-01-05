<?php

class Application_Form_UserSettings extends Diogo_Form
{
    public function init()
    {
        $element = new Zend_Form_Element_Text('Name');
        $element->setRequired();
        $element->setLabel(t('Name'));
        $this->addElement($element);

        $element = new Zend_Form_Element_Text('Email');
        $element->setLabel(t('Email'));
        $this->addElement($element);

        $this->addElement('ComboBox',
            'Privacy',
            array(
                'label' => t('Playlist\'s default privacy policy'),
                'multiOptions' => array(
                    'private' => t('Private'),
                    'public' => t('Public')
                    )
                )
            );

        $element = new Zend_Form_Element_Submit('submit');
        $element->setLabel(t('Save'));
        $this->addElement($element);

        $this->setAction('/user/index');
        $this->setAttrib('id', 'usersettings');
    }
}
