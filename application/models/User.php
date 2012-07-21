<?php

class User extends DZend_Model
{
    private $_id;
    private $_name;
    private $_email;
    private $_url;
    private $_loginArgs;
    private $_translate;

    public function __construct()
    {
        parent::__construct();
        $this->_translate = Zend_Registry::get('translate');
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
        $user->save();
    }

    /**
     * findByEmail Find the user row given the email.
     *
     * @param string $email User's email
     * @return DbTable_UserRow User's row, or null if user doesn't exists.
     */
    public function findByEmail($email)
    {
        return $this->_userDb->findRowByEmail($email);
    }

    /**
     * register Registers a new user and set it to be activated.
     *
     * @param string $name User's name
     * @param string $email User's email
     * @param string $password User's password
     * @return bool Returns true if operation is succeeded, false otherwise.
     */
    public function register($name, $email, $password)
    {
        $data = array();
        $data['name'] = $name;
        $data['email'] = $email;
        $data['password'] = sha1($password);
        $data['token'] = sha1(time(null) . implode('', $data));

        try {
            $row = $this->_userDb->insert($data);
            return true;
        } catch(Zend_Exception $e) {
            $this->_logger->error($e);
            return false;
        }
    }

    /**
     * deleteByEmail delete an account.
     *
     * @param mixed $email Email of the account to be deleted.
     * @return void
     */
    public function deleteByEmail($email)
    {
        $this->_userDb->deleteByEmail($email);
    }

    public function sendActivateAccountEmail($userRow)
    {
        $session = DZend_Session_Namespace::get('session');
        $mail = new Zend_Mail('UTF-8');

        $mail->setBodyHtml(
            $this->_translate->_(
                "Hi %s,<br/><br/>Welcome to AMUZI. To activate your account "
                . "just click on the link bellow:<br/><a href=\"%s\">%s</a>"
                . "<br/><br/>Enjoy!!<p>Best regards,<br/>AMUZI Team",
                $userRow->name,
                $userRow->getUrlToken(),
                $userRow->getUrlToken()
            )
        );
        $mail->setFrom('support@amuzi.net', 'AMUZI Team');
        $mail->addTo($userRow->email);
        $mail->setSubject($this->_translate->_("AMUZI -- Account activation"));

        try {
            $mail->send();
        } catch(Zend_Mail_Transport_Exception $e) {
            return false;
        }

        return true;
    }

    public function sendForgotPasswordEmail($userRow)
    {
        $session = DZend_Session_Namespace::get('session');
        $mail = new Zend_Mail('UTF-8');

        $mail->setBodyHtml(
            $this->_translate->_(
                "Hi %s,<br/><br/>Someone, hopefully you, requested a new "
                . "password on AMUZI. To make a new password, please click "
                . "the link bellow:<br/><br/><a href=\"%s\">%s</a><br/><br/>"
                . "Best regards,<br/>",
                "AMUZI Team",
                $userRow->name,
                $userRow->getForgotPasswordUrl(),
                $userRow->getForgotPasswordUrl()
            )
        );
        $mail->setFrom('support@amuzi.net', 'AMUZI Team');
        $mail->addTo($userRow->email);
        $mail->setSubject(
            $this->_translate->_("AMUZI -- New password request")
        );

        try {
            $mail->send();
        } catch(Zend_Mail_Transport_Exception $e) {
            return false;
        }

        return true;
    }
}

