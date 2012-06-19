<?php

class LastfmEntry extends AbstractEntry
{
    /**
     * __construct
     *
     * @return void
     */
    public function __construct($name, $pic, $artist, $musicTitle)
    {
        $this->_fields = array('name', 'pic', 'artist', 'musicTitle');
        $this->_data = array('name' => $name, 'pic' => $pic);
        foreach($this->_fields as $field)
            $this->_data[$field] = $$field;
    }

    /**
     * getArray
     *
     * @return void
     */
    public function getArray()
    {
        return $this->_data;
    }
}

