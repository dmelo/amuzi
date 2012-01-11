<?php

class DbTable_Playlist extends Diogo_Model_DbTable
{
    protected $_name = 'playlist';
    protected $_primary = 'id';
    protected $_rowClass = 'DbTable_PlaylistRow';

    public function findByName($name)
    {
        $user = $this->_session->user;
        return $this->findRowByUserIdAndName($user->id, $name);
    }

    public function search($q, $limit = 10, $offset = 0)
    {
        $where = $this->_db->quoteInto('name like ?', '%' . $q . '%') . $this->_db->quoteInto(' AND user_id = ?', $this->_session->user->id);
        return $this->fetchAll($this->select()->where($where)->limit($limit, $offset));
    }

    public function create($userId, $name)
    {
        $data = array(
            'user_id' => $userId,
            'name' => $name);
        $id = $this->insert($data);

        return $this->findRowById($id);
    }
}
