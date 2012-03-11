<?php

class DbTable_Playlist extends DZend_Model_DbTable
{
    protected $_name = 'playlist';
    protected $_primary = 'id';
    protected $_rowClass = 'DbTable_PlaylistRow';

    public function insert(array $data)
    {
        // All checks
        $firstLetter = substr($data['name'], 0, 1);
        if($firstLetter < 'a' || $firstLetter > 'z')
            throw new Zend_Db_Table_Exception(
                "playlist.name must start with a letter"
            );

        parent::insert($data);
    }

    public function findByName($name)
    {
        $user = $this->_session->user;
        return $this->findRowByUserIdAndName($user->id, $name);
    }

    public function search($q, $limit = 10, $offset = 0)
    {
        $where = $this->_db->quoteInto('name like ?', '%' . $q . '%');
        $where .= $this->_db->quoteInto(
            ' AND user_id = ?',
            $this->_session->user->id
        );
        return $this->fetchAll(
            $this->select()
            ->where($where)->order('name')->limit($limit, $offset)
        );
    }

    public function fetchAllUsers()
    {
        $select = $this->select()
            ->where(
                $this->_db->quoteInto('user_id = ?', $this->_session->user->id)
            )
            ->order('name');
        return $this->fetchAll($select);
    }

    public function create($userId, $name, $public)
    {
        $data = array(
            'user_id' => $userId,
            'name' => $name,
            'privacy' => $public ? 'public' : 'private'
            );
        $id = $this->insert($data);

        return $this->findRowById($id);
    }
}
