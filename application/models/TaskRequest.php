<?php

/**
 * TaskRequest
 *
 * @package Amuzi
 * @version 1.0
 * Amuzi - Online music
 * Copyright (C) 2010-2014  Diogo Oliveira de Melo
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
class TaskRequest extends DZend_Model
{
    /**
     * Add a task request.
     *
     * @param requestName Name of the request
     * @param ... Parameters.
     */
    public function addTask($requestName)
    {
        $args = array_slice(func_get_args(), 1);

        if ((
            $taskSetId = $this->_taskSetModel->findMostRecentTask(
                $requestName, $args
            )
        ) === false) {
            $taskSetId = $this->_taskSetModel->createTask($requestName, $args);
        }

        $taskSetRow = $this->_taskSetModel->findRowById($taskSetId);
        $expDate = new DateTime($taskSetRow->expiration);
        $now = new DateTime();
        if (
            $taskSetRow->expiration !== '0000-00-00 00:00:00'
            && $expDate < $now
        ) {
            $taskSetId = $this->_taskSetModel->createTask($requestName, $args);
        }


        $this->_logger->debug(
            "TaskRequest::addTask -> inserting taskSetId $taskSetId"
        );
        return $this->_taskRequestDb->insert(
            array(
                'task_set_id' => $taskSetId
            )
        );
    }

    public function findOpenTasks($requestName)
    {
        return $this->_taskSetModel->findOpenTasks($requestName);
    }

    public function closeTask($taskSetId)
    {
        return $this->_taskSetModel->closeTask($taskSetId);
    }
}
