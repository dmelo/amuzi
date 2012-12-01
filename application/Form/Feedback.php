<?php

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
