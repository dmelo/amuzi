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
        $jqueryUI = $domain . '/js/jquery-ui-1.8.16.custom.min.js';
        $jqueryForm = $domain . '/js/jquery.form.js';
        $jqueryAuto = $domain . '/js/jquery.autocomplete.js';
        $jqueryDMPlaylist = $domain . '/js/jquery.dmplaylist.js';
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
        $view->lightningPackerScript()->appendFile($jqueryForm);
        $view->lightningPackerScript()->appendFile($jqueryAuto);
        $view->lightningPackerScript()->appendFile($jqueryUI);
        $view->lightningPackerScript()->appendFile($jqueryDMPlaylist);
        $view->lightningPackerScript()->appendFile($domain . '/js/default.js');

        $view->lightningPackerLink()->appendStylesheet($domain .
            '/css/style.css');
        $view->lightningPackerLink()->appendStylesheet($domain .
            '/css/gallery.css');

        $view->lightningPackerLink()->appendStylesheet($domain .
            '/css/default.css');
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

    public function _initCache()
    {
        $frontend= array(
            'lifetime' => 2 * 24 * 60 * 60,
            'automatic_serialization' => true
        );

        $backend = array(
            'cache_dir' => 'tmp/'
        );

        $cache = Zend_Cache::factory('Output', 'File', $frontend, $backend);

        Zend_Registry::set('cache', $cache);
    }
}

