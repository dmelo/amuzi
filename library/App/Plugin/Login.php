<?php

class App_Plugin_Login extends DZend_Plugin_Login
{
    public function __construct()
    {
        parent::__construct();
        $this->_allowLogOutAccess = array(
            array('default', 'api'),
            array('default', 'docs'),
            array('default', 'index', 'about'),
            array('default', 'index', 'error'),
            array('default', 'index', 'test')
        );
    }
}
