<?php

require_once 'bootstrap.php';

class TaskManagerTest extends DZend_Test_PHPUnit_DatabaseTestCase
{
    public function testCreateTaskRequest()
    {
        $taskRequestModel = new TaskRequest();
        $taskRequestModel->addTask('SearchSimilar', 'U2', 'One');

        $ds = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet(
            $this->getConnection()
        );
        $ds->addTable('task_set', 'SELECT * FROM task_set');
        $ds->addTable('task_request', 'SELECT * FROM task_request');
        $ds->addTable('task_parameter', 'SELECT * FROM task_parameter');

        $dsFlat = $this->createXMLDataSet(
            dirname(__FILE__) . '/taskRequestInsertAssertion.xml'
        );

        $this->assertDataSetsEqual(
            $dsFlat, $this->filterTable(array('task_set', 'task_request', 'task_parameter'), $ds)
        );
    }
}
