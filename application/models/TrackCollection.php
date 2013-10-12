<?php

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
