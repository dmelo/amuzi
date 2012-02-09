<?php

require_once 'bootstrap.php';
class UserTest extends DZend_Test_PHPUnit_DatabaseTestCase
{
    public function testInsertUser()
    {
        $data = array(
            'facebook_id' => 'blablabla',
            'name' => 'Test Test',
            'email' => 'test@test.com',
            'url' => 'http://example.com',
            'privacy' => 'public'
        );

        $userDb = new DbTable_User();
        $userDb->register($data);
        $ds = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet(
            $this->getConnection()
        );
        $ds->addTable('user', 'SELECT * FROM user');
        $this->assertDataSetsEqual(
            $this->createFlatXmlDataSet(
                dirname(__FILE__) . '/userInsertAssertion.xml'
            ), $ds
        );
    }
}
