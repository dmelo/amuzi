<?php

/**
 * Log
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
class Log extends DZend_Model
{
    public function insert(
        $windowId,
        $action,
        $albumId = null,
        $trackId = null,
        $view = null,
        $searchId = null,
        $rfDepth = null
    )
    {
        $logActionRow = $this->_logActionDb->findRowByName($action);

        if (null === $logActionRow) {
            throw new Zend_Exception("Log action named $action doesn't exists");
        } else {
            $data = array(
                'user_id' => $this->_getUserId(),
                'window_id' => $windowId,
                'log_action_id' => $logActionRow->id,
                'album_id' => $albumId,
                'track_id' => $trackId,
                'view' => null === $view ? $this->_getUserRow()->view : $view,
                'search_id' => $searchId,
                'rf_depth' => $rfDepth
            );

            try {
                $this->_objDb->insert($data);
            } catch (Exception $e) {
                $this->_logger->err(
                    "Tried to insert db log " . print_r($data, true)
                    . " but an exception was throwed: " . $e->getMessage()
                );
                throw $e;
            }
        }
    }

    public function insertSparsity($zeros, $zerosSquared, $total)
    {
        $logActionRow = $this->_logActionDb->findRowByName('matrix_sparsity');
        if (null === $logActionRow) {
            throw new Zend_Exception("Log action named $action doesn't exists");
        } else {
            $data = array(
                'user_id' => $this->_getUserId(),
                'log_action_id' => $logActionRow->id,
                'zeros' => $zeros,
                'zeros_squared' => $zerosSquared,
                'total' => $total
            );

            try {
                $this->_objDb->insert($data);
            } catch (Exception $e) {
                $this->_logger->err(
                    "Tried to insert db log " . print_r($data, true)
                    . " but an exception was throwed: " . $e->getMessage()
                );
                throw $e;
            }
        }
    }

    public function findFilteredByLogActionId($logActionId)
    {
        $where = 'user_id != 1 and created > \'2013-12-10\'';
        if (is_int($logActionId)) {
            $where .= ' and log_action_id = ' . $logActionId;
        } elseif (is_array($logActionId)) {
            $where .= ' and log_action_id in ( ' . implode(', ', $logActionId) . ')';
        }

        return $this->_objDb->fetchAll($where);
    }

    public function findAuditableLog()
    {
        $where = ' created > \'2013-12-16\' AND log_action_id != 4 and user_id != 1';
        return $this->_objDb->fetchAll($where);
    }
}
