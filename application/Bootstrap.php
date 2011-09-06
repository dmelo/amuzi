<?php

/**
 * Bootstrap
 *
 * @package you2better-site
 * @version 1.0
 * @copyright Copyright (C) 2010 Diogo Oliveira de Melo. All rights reserved.
 * @author Diogo Oliveira de Melo <dmelo87@gmail.com>
 * @license GPL version 3
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    /**
     * _initPath
     *
     * @return void
     */
    protected function _initPath()
    {
        set_include_path(APPLICATION_PATH . '/models' . PATH_SEPARATOR .
            get_include_path());
        require_once 'Zend/Loader/Autoloader.php';
        $zendAutoloader = Zend_Loader_Autoloader::getInstance();
        $zendAutoloader->setFallbackAutoloader(true);
    }

    /**
     * _initView
     *
     * @return void
     */
    protected function _initView()
    {
        $this->bootstrap('domain');
        $domain = $this->getResource('domain');
        $jqueryUrl = 'http://ajax.googleapis.com/';
        $jqueryUrl .= 'ajax/libs/jquery/1.6.2/jquery.min.js';
        $jqueryProgressBar = $domain . '/js/jquery.progressbar.js';
        $this->bootstrap('layout');
        $layout = $this->getResource('layout');
        $view = $layout->getView();
        $view->addHelperPath('../library/LightningPackerHelper/',
            'Zend_View_Helper');

        $view->doctype('HTML5');
        $view->headMeta()->setCharset('UTF-8');
        $view->headTitle('You2Better');
        $view->lightningPackerScript()->appendFile($jqueryUrl);
        $view->lightningPackerScript()->appendFile($jqueryProgressBar);
        $view->lightningPackerScript()->appendFile($domain . '/js/default.js');
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

