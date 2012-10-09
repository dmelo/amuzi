<?php

/**
 * DbTable_TaskSet
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
class DbTable_TaskSet extends DZend_Db_Table
{
    public function findOpenTasks($taskTypeId)
    {
        $db = $this->getAdapter();
        $where = $db->quoteInto('task_type_id = ?', $taskTypeId) .
            $db->quoteInto(' AND done = ?', '0000-00-00 00:00:00');

        return $this->fetchAll($where);
    }
}
