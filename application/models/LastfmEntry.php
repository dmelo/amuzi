<?php

class LastfmEntry extends AbstractEntry
{
    /**
     * __construct
     *
     * @return void
     */
    public function __construct(
        $name,
        $cover,
        $artist,
        $musicTitle,
        $similarity = null
    )
    {
        $this->_fields = array(
            'name',
            'cover',
            'artist',
            'musicTitle',
            'similarity'
        );
        $this->_data = array('name' => $name, 'cover' => $cover);
        foreach($this->_fields as $field)
            if (null !== $$field)
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

