<?php

/**
 * Bootstrap
 *
 * @package Amuzi
 * @version 1.0
 * Amuzi - Online music
 * Copyright (C) 2010-2014  Diogo Oliveira de Melo
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

        $view->headTitle('Amuzi');
        $view->addHelperPath('../application/views/helpers', 'View_Helper');
        $view->addHelperPath(
            '../vendor/dmelo/dzend/library/DZend/View/Helper', 'DZend_View_Helper'
        );
        $view->addScriptPath('../application/views/scripts');

        if ('production' !== APPLICATION_ENV) {
            $js = array(
                'ie-compatibility.js',
                'jquery.js',
                'jquery.i18n.js',
                'i18n-dict.js',
                'jquery.url.js',
                'stacktrace.js',
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
                'log.js',
                'resultset.js',
                'jquery.cookie.js',
                'commands.js',
                'jquery.bootstrapMessage.js',
                'jquery.bootstrapLoadModal.js',
                'idangerous.swiper.js',
                'cssrule.js',
                'default.js',
                'mozaic.js',
                'incboard-cell.js',
                'incboard-board.js',
                'incboard.js',
                'search-exec.js',
                'bootstrap-tutorial.js',
                'jquery.subtitle.js',
            );
        } else {
            $js = array(
                'ie-compatibility.js',
                'jquery.js',
                'jquery.i18n.js',
                'jquery.url.js',
                'all-p1.js',
                'all-p2.js',
                'flash_detect.js',
                'default.js',
                'mozaic.js',
                'incboard-cell.js',
                'incboard-board.js',
                'incboard.js',
                'search-exec.js',
                'bootstrap-tutorial.js',
                'jquery.subtitle.js'
            );
        }

        Zend_Registry::set('js', $js);

        if ('production' !== APPLICATION_ENV) {
            $css = array(
                'jplayer.pink.flag.css',
                'player.css',
                'miniplayer.css',
                'resultset.css',
                'bootstrap.css',
                'github-badge.css',
                'bootstrap-helpers.css',
                'default.css',
                'incboard.css',
                'jquery.subtitle.css',
                'idangerous.swiper.css',
                'mozaic.css'
            );
        } else {
            $css = array(
                'all.css'
            );
        }

        Zend_Registry::set('css', $css);

        $config = new Zend_Config_Ini(
            APPLICATION_PATH . "/configs/application.ini", APPLICATION_ENV
        );

        $view->facebookId = $config->facebook->id;
        $view->facebookSecret = $config->facebook->secret;
        $view->facebookChannel = $domain . '/channel.html';

        Zend_Registry::set('facebookId', $config->facebook->id);
        Zend_Registry::set('facebookSecret', $config->facebook->secret);

    }

    public function _initDateTime()
    {
        date_default_timezone_set('America/Sao_Paulo');
    }

    public function _initRoutes()
    {
        $router = Zend_Controller_Front::getInstance()->getRouter();
        $router->addRoute(
            'artist',
            new Zend_Controller_Router_Route(
                'artist/:name',
                array(
                    'module' => 'default',
                    'controller' => 'artist',
                    'action' => 'info'
                )
            )
        );

        $router->addRoute(
            'album',
            new Zend_Controller_Router_Route(
                'album/:artist/:name',
                array(
                    'module' => 'default',
                    'controller' => 'album',
                    'action' => 'info'
                )
            )
        );
    }

    public function _initLocale()
    {
        $this->bootstrap('path');
        $this->bootstrap('logger');
        $this->bootstrap('db');
        $auth = Zend_Auth::getInstance();
        $lang = 'en_US';
        if ($auth->hasIdentity()) {
            $userModel = new User();
            $userRow = $userModel->findByEmail($auth->getIdentity());
            $lang = $userRow->lang;
        }
        
        $locale = new Zend_Locale($lang);
        Zend_Registry::set('locale', $locale);
    }
}
