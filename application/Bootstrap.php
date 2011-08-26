<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initPath() 
    {
        set_include_path(APPLICATION_PATH . '/models' . PATH_SEPARATOR . get_include_path());
        require_once 'Zend/Loader/Autoloader.php';
        $zendAutoloader = Zend_Loader_Autoloader::getInstance();
        $zendAutoloader->setFallbackAutoloader(true);   
    }

    protected function _initView()
    {
	$this->bootstrap('layout');
	$layout = $this->getResource('layout');
	$view = $layout->getView();
	$view->addHelperPath('../application/views/helpers/', 'Zend_View_Helper');
	$view->doctype('HTML5');
	$view->headMeta()->setCharset('UTF-8');
    }


}

