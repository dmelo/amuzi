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

    public function create($userId, $name)
    {
        $data = array(
            'user_id' => $userId,
            'name' => $name);
        $id = $this->insert($data);

        return $this->findRowById($id);
    }
}
