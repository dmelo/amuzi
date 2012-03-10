<?php

class Track
{
    protected $_trackDb;

    public function __construct()
    {
        $this->_trackDb = new DbTable_Track();
    }

    /**
     * findRowById Find a row, given it's ID.
     *
     * @param int $id Row ID
     * @return Returns the asked row.
     */
    public function findRowById($id)
    {
        return $this->_trackDb->findRowById($id);
    }

}
