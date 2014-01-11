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
interface DbTable_iTrackCollectionRow
{
    /**
     * getTrackListSync An array containing the tracks collection, with the
     * tracks also represented as array
     *
     * @return void
     */
    public function getTrackListSync();

    /**
     * playTime The sum of tracks duration
     *
     * @return int Return the time in seconds.
     */
    public function playTime();

    /**
     * getCover Get the URL with the image representing the collection.
     *
     * @return string Returns the URL for the image.
     */
    public function getCover();

    /**
     * getType The type of collection, can be either 'playlist' or 'album'.
     *
     * @return string Returns the collection type.
     */
    public function getType();

    /**
     * getArtistName Get the name of the collection to be displayed as cover.
     *
     * @return string Returns the name of the collection.
     */
    public function getCoverName();

    /**
     * Export the collection to an object ready to be transformed into the JSON
     * that will be interpreted by the client.
     */
    public function export();
}
