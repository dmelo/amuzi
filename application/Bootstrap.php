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

class Bootstrap extends DZend_Application_Bootstrap_Bootstrap
{
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
        $js[] = $domainJs . 'jquery.js';
        $js[] = $domainJs . 'jquery.browser.min.js';
        $js[] = $domainJs . 'jquery.jplayer.js';
        $js[] = $domainJs . 'jplayer.playlist.js';
        $js[] = $domainJs . 'jquery.jplayer.inspector.js';
        $js[] = $domainJs . 'themeswitcher.js';
        $js[] = $domainJs . 'jquery-ui-1.8.20.custom.min.js';
        $js[] = $domainJs . 'jquery.progressbar.js';
        $js[] = $domainJs . 'jquery.placeholder.min.js';
        $js[] = $domainJs . 'jquery.form.js';
        $js[] = $domainJs . 'jquery.tableofcontents.js';
        $js[] = $domainJs . 'resultset.js';
        $js[] = $domainJs . 'commands.js';
        $js[] = $domainJs . 'bootstrap-dropdown.js';
        $js[] = $domainJs . 'bootstrap-alert.js';
        $js[] = $domainJs . 'jquery.bootstrapMessage.js';
        $js[] = $domainJs . 'bootstrap-modal.js';
        $js[] = $domainJs . 'jquery.bootstrapLoadModal.js';
        $js[] = $domainJs . 'jquery.url.js';
        $js[] = $domainJs . 'default.js';

        $css = array();
        $css[] = $domainCss . 'prettify-jPlayer.css';
        $css[] = $domainCss . 'jplayer.pink.flag.css';
        $css[] = $domainCss . 'player.css';
        $css[] = $domainCss . 'gallery.css';
        $css[] = $domainCss . 'miniplayer.css';
        $css[] = $domainCss . 'resultset.css';
        $css[] = $domainCss . 'bootstrap.css';
        $css[] = $domainCss . 'github-badge.css';
        $css[] = $domainCss . 'default.css';

        $this->bootstrap('layout');
        $layout = $this->getResource('layout');
        $view = $layout->getView();
        $view->addHelperPath(
            '../library/LightningPackerHelper/',
            'Zend_View_Helper'
        );
        $view->addHelperPath('../application/views/helpers', 'View_Helper');

        $view->doctype('HTML5');
        $view->headMeta()->setCharset('UTF-8');
        $view->headTitle('AMUZI');

        foreach($js as $item)
            $view->lightningPackerScript()->appendFile($item);

        foreach($css as $item)
            $view->lightningPackerLink()->appendStylesheet($item);

        $this->bootstrap('translate');
        $view->translate = Zend_Registry::get('translate');
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

    public function _initDateTime()
    {
        date_default_timezone_set('America/Sao_Paulo');
    }
}
