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
        $domainJs = $this->getResource('domain') . '/js/';
        $domainCss = $this->getResource('domain') . '/css/';
        $js = array();
        $js[] = 'http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js';
        $js[] = 'http://jplayer.org/latest/js/jquery.jplayer.min.js';
        $js[] = 'http://jplayer.org/latest/js/jplayer.playlist.min.js';
        $js[] = 'http://jplayer.org/latest/js/jquery.jplayer.inspector.js';
        $js[] = 'http://jplayer.org/js/themeswitcher.js';
        $js[] = $domainJs . 'jquery.progressbar.js';
        $js[] = $domainJs . 'jquery-ui-1.8.16.custom.min.js';
        $js[] = $domainJs . 'jquery.form.js';
        $js[] = $domainJs . 'jquery.autocomplete.js';
        $js[] = $domainJs . 'default.js';

        $css = array();
        $css[] = 'http://jplayer.org/js/prettify/prettify-jPlayer.css';
        $css[] = 'http://jplayer.org/latest/skin/pink.flag/jplayer.pink.flag.css';
        $css[] = $domainCss . 'style.css';
        $css[] = $domainCss . 'gallery.css';
        $css[] = $domainCss . 'miniplayer.css';
        $css[] = $domainCss . 'default.css';

        $this->bootstrap('layout');
        $layout = $this->getResource('layout');
        $view = $layout->getView();
        $view->addHelperPath('../library/LightningPackerHelper/',
            'Zend_View_Helper');

        $view->doctype('HTML5');
        $view->headMeta()->setCharset('UTF-8');
        $view->headTitle('You2Better');

        foreach($js as $item)
            $view->lightningPackerScript()->appendFile($item);

        foreach($css as $item)
            $view->lightningPackerLink()->appendStylesheet($item);
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

