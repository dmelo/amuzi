<?php

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
        echo dirname(__FILE__) . PHP_EOL;

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
