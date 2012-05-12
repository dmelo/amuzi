<?php

class DbTable_UserRow extends DZend_Model_DbTableRow
{
    public function save()
    {
        $this->setTable(new DbTable_User());
        parent::save();
    }

    public function getUrlToken()
    {
        return Zend_Registry::get('domain') . '/Auth/index/activate/email/' . urlencode($this->email) . '/token/' . $this->token;
    }

    public function getForgotPasswordUrl()
    {
        $config = new Zend_Config_Ini(APPLICATION_PATH . "/configs/application.ini", APPLICATION_ENV);
        $time = time(null);
        $hash = sha1($this->email . $time . $config->salt);

        return Zend_Registry::get('domain') . '/Auth/index/resetpassword/email/' . $this->email . '/time/' . $time . '/hash/' . $hash;
    }

    public function isForgotPasswordUrlValid($time, $hash) {
        $config = new Zend_Config_Ini(APPLICATION_PATH . "/configs/application.ini", APPLICATION_ENV);
        return (sha1($this->email . $time . $config->salt) === $hash);
    }
}
