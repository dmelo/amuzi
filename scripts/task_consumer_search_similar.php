<?php

/**
 *
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
require_once '../scripts/lib.php';

$taskSetModel = new TaskSet();
$musicSimilarityModel = new MusicSimilarity();

$rowSet = $taskSetModel->findOpenTasks('SearchSimilar');

foreach ($rowSet as $row) {
    echo $row->id . "#" . $row->param0 . "#" . $row->param1 . "#" . PHP_EOL;
    $ret = $musicSimilarityModel->getSimilarSync($row->param0, $row->param1, null === $row->param2 ? 'track' : $row->param2);
    echo print_r($ret[0], true) . PHP_EOL;
    $done = date('Y-m-d H:i:s', time());
    $expiration = date('Y-m-d H:i:s', strtotime($done . ' + 1 month'));

    $row->done = $done;
    $row->expiration = $expiration;
    $row->save();
}
