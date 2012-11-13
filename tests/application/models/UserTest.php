<?php

/**
 * UserTest
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
require_once 'bootstrap.php';
class UserTest extends DZend_Test_PHPUnit_DatabaseTestCase
{
    public function testInsertUser()
    {
        $data = array(
            'name' => 'Test Test',
            'email' => 'test@test.com',
            'password' => sha1('abc'),
            'privacy' => 'public',
            'token' => '',
            'url' => ''
        );

        $userDb = new DbTable_User();
        $userDb->register($data);
        $ds = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet(
            $this->getConnection()
        );
        $ds->addTable('user', 'SELECT * FROM user');

        $this->assertDataSetsEqual(
            $this->createXMLDataSet(
                dirname(__FILE__) . '/userInsertAssertion.xml'
            ), $this->filterTable('user', $ds)
        );
    }

    public function login()
    {
        $form = new Auth_Model_Form_Login();
        $form->setAction('/Auth/index/login');
    }
}
