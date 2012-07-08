<?php

class Track extends DZend_Model
{
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

    /**
     * insert Insert a row.
     *
     * @param mixed $data
     * @return DbTable_TrackRow Returns the inserted row.
     */
    public function insert($data)
    {
        return $this->_trackDb->insert($data);
    }

}
