<?php

/**
 * 
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
abstract class AbstractControllerTest
    extends DZend_Test_PHPUnit_ControllerTestCase
{
    public function testLogin()
    {
        $params = array(
            'email' => 'dmelo87@gmail.com',
            'password' => 'cafess123',
            'submit' => 'Login'
        );

        $this->request->setMethod('post')
            ->setPost($params)->setParams($params);

        $this->dispatch('/Auth/index/login');
        $this->assertRedirectTo('/');

        $this->resetRequest()->resetResponse();
        $this->request->setMethod('get')->setPost(array());
    }

    public function assertLoginForm()
    {
        $this->assertQuery('form input#email');
        $this->assertQuery('form input#password');
        $this->assertQuery('form input#submit');
    }

    public function assertAjaxLoginForm($uri)
    {
        $this->assertIsAjax($uri);
        $this->assertLoginForm();
    }
}
