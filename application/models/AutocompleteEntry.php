<?php

class AutocompleteEntry extends AbstractEntry
{
    public function __construct($artist, $musicTitle, $cover, $type)
    {
        $this->_fields = array(
            'name',
            'cover',
            'artist',
            'musicTitle',
            'type'
        );

        foreach ($this->_fields as $field) {
            if ('name' !== $field) {
                $this->$field = $$field;
            }
        }
    }

    public function __get($key)
    {
        if ('name' === $key) {
            return "{$this->artist} - {$this->musicTitle}";
        } elseif ('cover' === $key) {
            $ret = '/img/album.png';
            if (null !== $this->_data['cover']) {
                $ret = $this->_data['cover'];
            }

            return $ret;
        } else {
            return parent::__get($key);
        }
    }

    public function getArray()
    {
        $ret = array();
        foreach ($this->_fields as $field) {
            $ret[$field] = $this->$field;
        }

        return $ret;
    }
}
