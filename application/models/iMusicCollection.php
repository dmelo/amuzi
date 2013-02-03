<?php

interface iMusicCollection
{
    /**
     * getTrackListAsArray An array containing the tracks collection, with the
     * tracks also represented as array
     *
     * @return void
     */
    public function getTrackListAsArray();

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
}
