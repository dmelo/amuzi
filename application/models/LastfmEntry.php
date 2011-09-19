<?php

class LastfmEntry extends AbstractEntry
{
    /**
     * __construct
     *
     * @return void
     */
    public function __construct($name, $pic)
    {
        $this->_fields = array('name', 'pic');
        $this->_data = array('name' => $name, 'pic' => $pic);
    }

    /**
     * getArray
     *
     * @return void
     */
    public function getArray()
    {
        $item = array();
        $item['name'] = $this->name;
        $item['pic'] = $this->pic;

        return $item;
    }
}

