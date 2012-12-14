<?php

/**
 * Bootstrap
 *
 * @package Amuzi
 * @version 1.0
 * Amuzi - Online music
 * Copyright (C) 2010-2012  Diogo Oliveira de Melo
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
        $js = array();
        $js[] = $domainJs . 'jquery.js';
        $js[] = $domainJs . 'facebook-connect.js';
        $js[] = $domainJs . 'bootstrap.js';
        $js[] = $domainJs . 'jquery.browser.min.js';
        $js[] = $domainJs . 'jquery.jplayer.js';
        $js[] = $domainJs . 'jplayer.playlist.js';
        $js[] = $domainJs . 'jquery.jplayer.inspector.js';
        $js[] = $domainJs . 'themeswitcher.js';
        $js[] = $domainJs . 'jquery-ui-1.9.2.custom.js';
        $js[] = $domainJs . 'jquery.progressbar.js';
        $js[] = $domainJs . 'jquery.placeholder.min.js';
        $js[] = $domainJs . 'jquery.form.js';
        $js[] = $domainJs . 'jquery.tableofcontents.js';
        $js[] = $domainJs . 'resultset.js';
        $js[] = $domainJs . 'jquery.cookie.js';
        $js[] = $domainJs . 'commands.js';
        $js[] = $domainJs . 'jquery.bootstrapMessage.js';
        $js[] = $domainJs . 'jquery.bootstrapLoadModal.js';
        $js[] = $domainJs . 'jquery.url.js';
        $js[] = $domainJs . 'bootstrap-slide.js';
        $js[] = $domainJs . 'default.js';
        $js[] = $domainJs . 'incboard-cell.js';
        $js[] = $domainJs . 'incboard-board.js';
        $js[] = $domainJs . 'incboard.js';
        $js[] = $domainJs . 'bootstrap-tutorial.js';
        $js[] = $domainJs . 'jquery.subtitle.js';

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
        $css[] = $domainCss . 'incboard.css';
        $css[] = $domainCss . 'jquery.subtitle.css';
        $css[] = $domainCss . 'bootstrap-slide.css';

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
            $view->lightningPackerScript()->appendFile("$item?v=$version");

        foreach($css as $item)
            $view->lightningPackerLink()->appendStylesheet("$item?v=$version");

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
            'cache_dir' => '../public/tmp/'
        );

        $cache = Zend_Cache::factory('Output', 'File', $frontend, $backend);

        Zend_Registry::set('cache', $cache);
    }

    public function _initDateTime()
    {
        date_default_timezone_set('America/Sao_Paulo');
    }
}
