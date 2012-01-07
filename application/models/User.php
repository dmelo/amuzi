<?php

class User
{
    private $_id;
    private $_facebookId;
    private $_name;
    private $_email;
    private $_url;
    private $_loginArgs;
    private $_userDb;

    public function __construct()
    {
        $this->_loginArgs = array('facebook_id', 'name', 'email', 'url');
        $this->_userDb = new DbTable_User();
    }

    public function login($params)
    {
        $data = array();
        foreach($this->_loginArgs as $arg) {
            if(array_key_exists($arg, $params)) {
                $this->{'_' . $arg} = $params[$arg];
                $data[$arg] = $params[$arg];
            }
        }

        $row = $this->_userDb->register($data);
        return $row->id;
    }

    public function findRowByFacebookId($facebookId)
    {
        return $this->_userDb->findRowByFacebookId($facebookId);
    }

    public function getSettings()
    {
        $ret = array();
        $user = $this->_userDb->findCurrent();
        $ret['name'] = $user->name;
        $ret['email'] = $user->email;
        $ret['privacy'] = $user->privacy;

        return $ret;
    }

    public function setSettings($params)
    {
        $user = $this->_userDb->findCurrent();
        $user->name = $params['name'];
        $user->email = $params['email'];
        $user->privacy = $params['privacy'];
        $user->setTable(new DbTable_User());
        $user->save();
    }
}

