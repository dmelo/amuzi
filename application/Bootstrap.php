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
        $this->bootstrap('translate');
        $this->bootstrap('layout');
        $this->bootstrap('domain');
        $domain = Zend_Registry::get('domain');
        $layout = $this->getResource('layout');
        $view = $layout->getView();
        $view->translate = Zend_Registry::get('translate');

        $view->addHelperPath(
            '../library/LightningPackerHelper/',
            'Zend_View_Helper'
        );

        $view->addHelperPath('../application/views/helpers', 'View_Helper');
        $view->addHelperPath(
            '../library/DZend/View/Helper', 'DZend_View_Helper'
        );
        $view->addScriptPath('../application/views/scripts');

        $js = array(
            'ie-compatibility.js',
            'jquery.js',
            'log.js',
            'facebook-connect.js',
            'bootstrap.js',
            'jquery.browser.min.js',
            'jquery.jplayer.js',
            'jplayer.playlist.js',
            'jplayer.playlist.ext.js',
            'themeswitcher.js',
            'jquery-ui-1.9.2.custom.js',
            'jquery.progressbar.js',
            'jquery.placeholder.min.js',
            'jquery.form.js',
            'jquery.tableofcontents.js',
            'resultset.js',
            'jquery.cookie.js',
            'commands.js',
            'jquery.bootstrapMessage.js',
            'jquery.bootstrapLoadModal.js',
            'jquery.url.js',
            'bootstrap-slide.js',
            'cssrule.js',
            'default.js',
            'mozaic.js',
            'incboard-cell.js',
            'incboard-board.js',
            'incboard.js',
            'search-exec.js',
            'bootstrap-tutorial.js',
            'jquery.subtitle.js'
        );
        Zend_Registry::set('js', $js);

        $css = array(
            'jplayer.pink.flag.css',
            'player.css',
            'miniplayer.css',
            'resultset.css',
            'bootstrap.css',
            'github-badge.css',
            'default.css',
            'incboard.css',
            'jquery.subtitle.css',
            'bootstrap-slide.css',
            'mozaic.css'
        );
        Zend_Registry::set('css', $css);

        $config = new Zend_Config_Ini(
            APPLICATION_PATH . "/configs/application.ini", APPLICATION_ENV
        );

        $view->facebookId = $config->facebook->id;
        $view->facebookSecret = $config->facebook->secret;
        $view->facebookChannel = $domain . '/channel.html';

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
