<?php

require_once 'bootstrap.php';

class DbTableTest extends DZend_Test_PHPUnit_DatabaseTestCase
{
    public function testCamelToUnderscore()
    {
        $this->assertEquals("a_b_c_d", DbTable_User::camelToUnderscore('ABCD'));
        $this->assertEquals("user", DbTable_User::camelToUnderscore('User'));
        $this->assertEquals("user_listen_playlist", DbTable_User::camelToUnderscore('UserListenPlaylist'));
    }
}
