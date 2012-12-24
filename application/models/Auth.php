<?php

/**
 * Auth
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
class Auth extends DZend_Model
{
    public function authenticate($email, $password)
    {
        $authAdapter = Zend_Registry::get('authAdapter');
        $authAdapter->setIdentity($email);
        $authAdapter->setCredential($password);

        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $auth->getIdentity();
        }

        return $auth->authenticate($authAdapter);
    }

    public function authenticateFacebook($email, $name)
    {

        if (null === $this->_userModel->findByEmail($email)) {
            $this->_userModel->register($name, $email, '');
            $userRow = $this->_userModel->findByEmail($email);
            $userRow->postRegister();
        }

        $authAdapter = new DZend_Auth_Adapter_Facebook();
        $authAdapter->setIdentity($email);

        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $auth->getIdentity();
        }

        return $auth->authenticate($authAdapter);
    }
}
