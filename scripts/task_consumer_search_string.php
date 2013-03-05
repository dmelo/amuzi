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
$lastfmModel = new Lastfm();
$albumModel = new Album();
$musicTrackLinkModel = new MusicTrackLink();
$lastfmModel = new Lastfm();

$rowSet = $taskSetModel->findOpenTasks('SearchString');

foreach ($rowSet as $row) {
    echo $row->id . '#' . $row->param0 . '#' . $row->param1 . '#' . PHP_EOL;
    if ('Album' === $row->param0) {
        $ret = $lastfmModel->searchAlbum($row->param1, 250);
        foreach ($ret as $r) {
            try {
                $album = $lastfmModel->getAlbum($r->artist, $r->musicTitle);
                $albumId = $albumModel->insert($album);
                $albumRow = $albumModel->findRowById($albumId);
                $albumRow->getTrackListSync();
                echo "Inserted: " . $r . PHP_EOL;
            } catch (Zend_Exception $e) {
                echo "Album $r is already inserted" . PHP_EOL;
            }
        }
    } elseif ('MusicTitle' === $row->param0) {
        $track = $lastfmModel->searchTrack($row->param1, 250);
    }

    $done = date('Y-m-d H:i:s', time());
    $expiration = date('Y-m-d H:i:s', strtotime($done . ' + 6 month'));

    $row->done = $done;
    $row->expiration = $expiration;
    $row->save();
}
