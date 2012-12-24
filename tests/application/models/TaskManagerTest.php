<?php

/**
 * TaskManagerTest
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

class TaskManagerTest extends DZend_Test_PHPUnit_DatabaseTestCase
{
    private $_taskRequestModel = null;

    private function _addTaskSet()
    {
        if (null === $this->_taskRequestModel) {
            $this->_taskRequestModel = new TaskRequest();
        }
        $this->_taskRequestModel->addTask('SearchSimilar', 'U2', 'One');
    }

    private function _getTaskTables()
    {
        $ds = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet(
            $this->getConnection()
        );
        $ds->addTable('task_type', 'SELECT * FROM task_type');
        $ds->addTable('task_set', 'SELECT * FROM task_set');
        $ds->addTable('task_request', 'SELECT * FROM task_request');
        $ds->addTable('task_parameter', 'SELECT * FROM task_parameter');

        return $this->filterTable(
            array('task_type', 'task_set', 'task_request', 'task_parameter'), $ds
        );
    }

    public function testCreateTaskRequest()
    {
        $this->_addTaskSet();
        $dsFlat = $this->createXMLDataSet(
            dirname(__FILE__) . '/taskRequestInsertAssertion.xml'
        );

        $this->assertDataSetsEqual(
            $dsFlat, $this->_getTaskTables()
        );
    }

    public function testCreateMultipleTaskRequests()
    {
        $this->_addTaskSet();
        $this->_addTaskSet();
        $this->_addTaskSet();
        $this->_addTaskSet();
        $dsFlat = $this->createXMLDataSet(
            dirname(__FILE__) . '/taskRequestInsertAssertion.xml'
        );
        $dsFlat->getTable('task_request')->addRow(array('id' => 2, 'task_set_id' => 1));
        $dsFlat->getTable('task_request')->addRow(array('id' => 3, 'task_set_id' => 1));
        $dsFlat->getTable('task_request')->addRow(array('id' => 4, 'task_set_id' => 1));

        $this->assertDataSetsEqual(
            $dsFlat, $this->_getTaskTables()
        );

    }

    public function testCloseTaskRequest()
    {
        $this->_addTaskSet();
        $rowSet = $this->_taskRequestModel->findOpenTasks('SearchSimilar');
        foreach ($rowSet as $row) {
            $this->_taskRequestModel->closeTask($row->id);
        }

        $dbTables = $this->_getTaskTables();
        $done = $dbTables->getTable('task_set')->getValue(0, 'done');
        $duration = $dbTables->getTable('task_type')->getValue(0, 'duration');

        $dsFlat = $this->createXMLDataSet(
            dirname(__FILE__) . '/taskRequestInsertAssertion.xml'
        );

        $dsFlat->getTable('task_set')->setValue(
            0, 'done', $done
        );
        $dsFlat->getTable('task_set')->setValue(
            0, 'expiration', date('Y-m-d H:i:s', strtotime("$done + $duration seconds"))
        );


        $this->assertDataSetsEqual(
            $dsFlat, $dbTables
        );
    }
}
