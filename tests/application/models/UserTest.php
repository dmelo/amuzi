<?php

require_once 'bootstrap.php';
class UserTest extends Zend_Test_PHPUnit_DatabaseTestCase
{
    private $_connectionMock;

    protected function getConnection()
    {
        $config = new Zend_Config_Ini(
            APPLICATION_PATH . '/configs/application.ini',
            'testing'
        );
        $connection = Zend_Db::factory(
            $config->resources->db->adapter,
            $config->resources->db->params
        );

        $this->_connectionMock = $this->createZendDbConnection(
            $connection, 'zfunittests'
        );
        Zend_Db_Table_Abstract::setDefaultAdapter($connection);

        return $this->_connectionMock;
    }

    protected function getDataSet()
    {
        return $this->createFlatXmlDataSet(
            dirname(__FILE__) . '/dataset.xml'
        );
    }

    public function testSanity()
    {
        $this->assertTrue(true);
    }
}
