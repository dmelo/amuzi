<?php

class Diogo_Application_Bootstrap_Bootstrap extends Zend_Application_Boostrap_Bootstrap
{
    /**
     * _initPath
     *
     * @return void
     */
    protected function _initPath()
    {
        set_include_path(APPLICATION_PATH . '/models' . PATH_SEPARATOR .
            APPLICATION_PATH . PATH_SEPARATOR . get_include_path());
        require_once 'Zend/Loader/Autoloader.php';
        $zendAutoloader = Zend_Loader_Autoloader::getInstance();
        $zendAutoloader->setFallbackAutoloader(true);
    }

    /**
     * _initDomain
     *
     * @return void
     */
    protected function _initDomain()
    {
        $domain = 'http://' . $_SERVER['HTTP_HOST'];
        Zend_Registry::set('domain', $domain);
        return $domain;
    }


}
