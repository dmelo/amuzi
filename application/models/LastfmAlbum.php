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

    public function getArray()
    {
        $ret = array();
        foreach ($this->_fields as $field) {
            if (is_array($this->$field)) {
                $ret[$field] = array();
                foreach ($this->$field as $item) {
                    $ret[$field][] = $item->getArray();
                }
            } else {
                $ret[$field] = $this->$field;
            }
        }

        return $ret;
    }
}
