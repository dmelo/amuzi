<?php

define('__DIOGO_SESSION__', 'Diogo_Session_Namespace_');

class Diogo_Session_Namespace extends Zend_Session_Namespace
{
    static public function get($namespace)
    {
        try {
            $session = new Zend_Session_Namespace($namespace, true);
            Zend_Registry::set(__DIOGO_SESSION__ . $namespace, $session);
            $ret = $session;
        } catch(Zend_Session_Exception $e) {
            $ret = Zend_Registry::get(__DIOGO_SESSION__ . $namespace);
        }

        return $ret;
    }
}
