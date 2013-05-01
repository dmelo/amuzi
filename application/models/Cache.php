<?php

class Cache
{
    public static function get()
    {
        try {
            $ret = Zend_Registry::get('cache');
        } catch (Zend_Exception $e) {
            $frontend= array(
                'lifetime' => 2 * 24 * 60 * 60,
                'automatic_serialization' => true
            );

            $backend = array(
                'cache_dir' => '../public/tmp/',
                'hashed_directory_level' => 2
            );

            $ret = Zend_Cache::factory('Output', 'File', $frontend, $backend);
            Zend_Registry::set('cache', $ret);
        }

        return $ret;
    }
}
