<?php

/**
 * 
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
require_once '../scripts/lib.php';

$logModel = new Log();
$logRowSet = $logModel->findFilteredByLogActionId(array(1, 2));
$incBoard = $default = array();

foreach ($logRowSet as $logRow) {
    if ('default' === $logRow->view) {
        $default[$logRow->userId]++;
        $defaultTotal++;
    } elseif ('incboard' === $logRow->view) {
        $incBoard[$logRow->userId]++;
        $incBoardTotal++;
    }
}

echo "default: " . print_r($default, true) . ". incBoard: " . print_r($incBoard, true) . "." . PHP_EOL;
echo "defaultTotal: $defaultTotal. incBoardTotal: $incBoardTotal" . PHP_EOL;
