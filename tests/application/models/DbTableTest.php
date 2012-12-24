<?php

/**
 * DbTableTest
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
require_once 'bootstrap.php';

class DbTableTest extends DZend_Test_PHPUnit_DatabaseTestCase
{
    public function testCamelToUnderscore()
    {
        $this->assertEquals("a_b_c_d", DbTable_User::camelToUnderscore('ABCD'));
        $this->assertEquals("user", DbTable_User::camelToUnderscore('User'));
        $this->assertEquals(
            "user_listen_playlist",
            DbTable_User::camelToUnderscore('UserListenPlaylist')
        );
    }
}
