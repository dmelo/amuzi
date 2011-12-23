<?php

class DbTable_Playlist extends Diogo_Model_DbTable
{
    protected $_name = 'playlist';
    protected $_primary = 'id';
    protected $_rowClass = 'DbTable_PlaylistRow';

    public function findByUserIdAndName($userId, $name)
    {
        $where = $this->_db->quoteInto('user_id = ? AND ', $userId) . $this->_db->quoteInto('name = ?', $name);
        return $this->fetchRow($where);
    }

    public function findByName($name)
    {
        $user = $this->_session->user;
        $where = $this->_db->quoteInto('user_id = ? AND ', $user->id) . $this->_db->quoteInto('name = ?', $name);
        return $this->fetchRow($where);
    }

    public function create($userId, $name)
    {
        $data = array(
            'user_id' => $userId,
            'name' => $name);
        $id = $this->insert($data);

        return $this->findById($id);
    }
}
