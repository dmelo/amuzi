<?php

class DbTable_User extends DZend_Db_Table
{
    /**
     * register Make sure the user is registered on the database.
     *
     * @param mixed $data Array that contains information about the user.
     * @return Zend_Db_Table_Row returns the user row.
     */
    public function register($data)
    {
        $ret = null;
        try {
            // Register the user.
            $id = $this->insert($data);
            $ret = $this->findRowById($id);
            $playlistDb = new DbTable_Playlist();
            $playlistRow = $playlistDb->create($ret->id, 'default');
            $ret->currentPlaylistId = $playlistRow->id;
            $ret->save();
        } catch(Zend_Db_Statement_Exception $e) {
            // In case the user is already registered, just update his info.

            $where = $this->_db->quoteInto(
                'facebook_id = ?', $data['facebook_id']
            );
            $this->update($data, $where);
            $ret = $this->findRowByFacebookId($data['facebook_id']);
        }

        return $ret;
    }

    public function findCurrent()
    {
        return $this->_session->user;
    }
}
