<?php

/**
 * User
 *
 * @package Amuzi
 * @version 1.0
 * Amuzi - Online music
 * Copyright (C) 2010-2013  Diogo Oliveira de Melo
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
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
        $ret['view'] = $user->view;

        return $ret;
    }

    public function setSettings($params)
    {
        // TODO: create log entry on db.
        $user = $this->_userDb->findCurrent();
        $user->name = $params['name'];
        $user->email = $params['email'];
        $user->privacy = $params['privacy'];
        $user->view = $params['view'];
        $user->save();
    }

    public function getView()
    {
        $user = $this->_userDb->findCurrent();
        return $user->view;
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
        $data['view'] = rand(0, 1) ? 'incboard' : 'default';

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
        $mail = new Zend_Mail('UTF-8');

        $msg = $this->_translate->_(
            "Hi %s,<br/><br/>Welcome to AMUZI. To activate your account "
            . "just click on the link bellow:<br/><a href=\"%s\">%s</a>"
            . "<br/><br/>Enjoy!!<p>Best regards,<br/>Diogo Oliveira de Melo",
            $userRow->name,
            $userRow->getUrlToken(),
            $userRow->getUrlToken()
        );
        $mail->setBodyHtml($msg);
        $mail->setFrom('support@amuzi.net', 'Diogo Oliveira de Melo');
        $mail->addTo($userRow->email);
        $mail->setSubject($this->_translate->_("AMUZI -- Account activation"));

        try {
            $mail->send();
            $this->_logger->debug(
                'User::sendActivateAccountEmail sending ok. --> ' . $msg
            );
        } catch(Zend_Mail_Transport_Exception $e) {
            $this->_logger->error($e);
            return false;
        }

        return true;
    }

    public function sendForgotPasswordEmail($userRow)
    {
        $mail = new Zend_Mail('UTF-8');

        $msg = $this->_translate->_(
            "Hi %s,<br/><br/>Someone, hopefully you, requested a new "
            . "password on AMUZI. To make a new password, please click "
            . "the link bellow:<br/><br/><a href=\"%s\">%s</a><br/><br/>"
            . "Best regards,<br/>"
            . "Diogo Oliveira de Melo",
            $userRow->name,
            $userRow->getForgotPasswordUrl(),
            $userRow->getForgotPasswordUrl()
        );

        $mail->setBodyHtml($msg);
        $mail->setFrom('support@amuzi.net', 'Diogo Oliveira de Melo');
        $mail->addTo($userRow->email);
        $mail->setSubject(
            $this->_translate->_("AMUZI -- New password request")
        );

        try {
            $mail->send();
            $this->_logger->debug(
                'User::sendForgotPasswordEmail sending ok. --> ' . $msg
            );
        } catch(Zend_Mail_Transport_Exception $e) {
            $this->_logger->error(
                'User::sendForgotPasswordEmail error while sending email'
                . ' to user: ' . $userRow->email
            );
            return false;
        }

        return true;
    }
}
