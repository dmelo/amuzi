<?php

/**
 * TrackCollection
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
class TrackCollection
{
    /**
     * trackList An array of tracks.
     *
     * @var array
     */
    public $trackList;

    /**
     * name The name of the collection
     *
     * @var string
     */
    public $name;

    /**
     * type The collection type, 'playlist' or 'album'.
     *
     * @var string
     */
    public $type;

    /**
     * repeat Flag to indicate infinite loop play. 0 or 1.
     *
     * @var int
     */
    public $repeat;

    /**
     * shuffle Flag to indicate if must be shuffled. 0 or 1.
     *
     * @var integer
     */
    public $shuffle;

    /**
     * currentTrack Current track id.
     *
     * @var int
     */
    public $currentTrack;
}
