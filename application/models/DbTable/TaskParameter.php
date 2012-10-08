<?php

/**
 * DbTable_TaskParameter
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
class DbTable_TaskParameter extends DZend_Db_Table
{
    /**
     * Given a set of task_set_ids and the parameters, find the most recent
     * task_set_id that matches those parameters.
     *
     * @param $ids List of task_set_ids.
     * @param $args List of parameters.
     *
     * @return The most recent task_set_id that matches or false if there is no
     * match.
     */
    public function findMostRecentTaskSetId($ids, $args)
    {
        $db = $this->getAdapter();
        $sql = '';
        $first = true;

        // The parameter must match one of the task_set_ids.
        $sqlAux = '(';

        foreach ($ids as $id) {
            if ($first) {
                $first = false;
            } else {
                $sqlAux .= ' OR ';
            }

            $sqlAux .= $db->quoteInto(' task_set_id = ? ', $id);
        }

        $sqlAux .= ')';

        // The parameter row must match one of the parameters.
        $first = true;
        foreach ($args as $order => $param) {
            if ($first) {
                $first = false;
            } else {
                $sql .= " OR ";
            }
            $sql .= $db->quoteInto(" ( $sqlAux AND `order` = ? AND `param` = ? ) ", $order, $param);
        }

        $this->_logger->debug("DbTable_TaskParameter::findMostRecentTaskSetId -> " . $sql);

        $rowSet = $this->fetchAll($sql);
        $result = array();

        foreach ($rowSet as $row) {
            $tsi = $row->taskSetId;
            if (array_key_exists($tsi, $result)) {
                $result[$row->taskSetId]++;
            } else {
                $result[$row->taskSetId] = 1;
            }
        }

        $max = -1;

        foreach ($results as $tsi => $count) {
            if ($count === count($args) && $tsi > $max) {
                $max = $tsi;
            }
        }

        return -1 === $max ? false : $max;
    }
}
