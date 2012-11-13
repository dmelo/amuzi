<?php

/**
 * TaskManagerTest
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
