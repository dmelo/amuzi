<?php

/**
 * Bootstrap
 *
 * @package Amuzi
 * @version 1.0
 * Amuzi - Online music
 * Copyright (C) 2010-2013  Diogo Oliveira de Melo
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
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
        $version = file_get_contents('../version.txt');
        $this->bootstrap('domain');
        $domainJs = $this->getResource('domain') . '/js/';
        $domainCss = $this->getResource('domain') . '/css/';
        $js = array(
            $domainJs . 'ie-compatibility.js',
            $domainJs . 'jquery.js',
            $domainJs . 'log.js',
            $domainJs . 'facebook-connect.js',
            $domainJs . 'bootstrap.js',
            $domainJs . 'jquery.browser.min.js',
            $domainJs . 'jquery.jplayer.js',
            $domainJs . 'jplayer.playlist.js',
            $domainJs . 'jplayer.playlist.ext.js',
            $domainJs . 'themeswitcher.js',
            $domainJs . 'jquery-ui-1.9.2.custom.js',
            $domainJs . 'jquery.progressbar.js',
            $domainJs . 'jquery.placeholder.min.js',
            $domainJs . 'jquery.form.js',
            $domainJs . 'jquery.tableofcontents.js',
            $domainJs . 'resultset.js',
            $domainJs . 'jquery.cookie.js',
            $domainJs . 'commands.js',
            $domainJs . 'jquery.bootstrapMessage.js',
            $domainJs . 'jquery.bootstrapLoadModal.js',
            $domainJs . 'jquery.url.js',
            $domainJs . 'bootstrap-slide.js',
            $domainJs . 'cssrule.js',
            $domainJs . 'default.js',
            $domainJs . 'mozaic.js',
            $domainJs . 'incboard-cell.js',
            $domainJs . 'incboard-board.js',
            $domainJs . 'incboard.js',
            $domainJs . 'search-exec.js',
            $domainJs . 'bootstrap-tutorial.js',
            $domainJs . 'jquery.subtitle.js'
        );

        $css = array(
            $domainCss . 'jplayer.pink.flag.css',
            $domainCss . 'player.css',
            $domainCss . 'miniplayer.css',
            $domainCss . 'resultset.css',
            $domainCss . 'bootstrap.css',
            $domainCss . 'github-badge.css',
            $domainCss . 'default.css',
            $domainCss . 'incboard.css',
            $domainCss . 'jquery.subtitle.css',
            $domainCss . 'bootstrap-slide.css',
            $domainCss . 'mozaic.css'
        );

        $this->bootstrap('layout');
        $layout = $this->getResource('layout');
        $view = $layout->getView();
        $view->addHelperPath(
            '../library/LightningPackerHelper/',
            'Zend_View_Helper'
        );

        $view->addHelperPath('../application/views/helpers', 'View_Helper');
        $view->addHelperPath(
            '../library/DZend/View/Helper', 'DZend_View_Helper'
        );
        $view->addScriptPath('../application/views/scripts');

        $view->doctype('HTML5');
        $view->headMeta()->setCharset('UTF-8');
        $view->headTitle('AMUZI - Online music player');

        foreach($js as $item) {
            $view->lightningPackerScript()->appendFile("$item?v=$version");
        }

        foreach($css as $item) {
            $view->lightningPackerLink()->appendStylesheet("$item?v=$version");
        }

        $this->bootstrap('translate');
        $view->translate = Zend_Registry::get('translate');

        $config = new Zend_Config_Ini(
            APPLICATION_PATH . "/configs/application.ini", APPLICATION_ENV
        );

        $view->facebookId = $config->facebook->id;
        $view->facebookSecret = $config->facebook->secret;
        $view->facebookChannel = $this->getResource('domain') . '/channel.html';
    }

    public function _initCache()
    {
        $frontend= array(
            'lifetime' => 2 * 24 * 60 * 60,
            'automatic_serialization' => true
        );

        $backend = array(
            'cache_dir' => '../public/tmp/',
            'hashed_directory_level' => 2
        );

        $cache = Zend_Cache::factory('Output', 'File', $frontend, $backend);
        Zend_Registry::set('cache', $cache);
    }

    public function _initDateTime()
    {
        date_default_timezone_set('America/Sao_Paulo');
    }
}
