<?php

class LastfmAlbum extends AbstractEntry
{
    public function __construct($name, $cover, $artist, $trackList)
    {
        $this->_fields = array(
            'name',
            'cover',
            'artist',
            'trackList'
        );

        foreach ($this->_fields as $field) {
            if (null !== $$field) {
                $this->_data[$field] = $$field;
            }
        }
    }
}
