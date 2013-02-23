<?php

class AmuziSearch extends DZend_Model
{
    protected $_ports = array(
        'album' => 3675, 'track' => 3676, 'artist' => 3677
    );
    protected $_keys = array('album', 'track', 'artist');

    public function autocomplete($q, $type, $limit = 5)
    {
        $ret = array();
        $q = preg_replace('/ - ?/', 'A', strtolower($q));
        ob_implicit_flush();
        if (!in_array($type, $this->_keys)) {
            $this->_logger->debug("AmuziSearch::autocomplete wrong type $type.");
            return $ret;
        }

        $sock = false;
        if (($sock = socket_create(
            AF_INET, SOCK_STREAM, SOL_TCP
        )) === false) {
            $this->_logger->err("AmuziSearch::autocomplete error on socket_create. Reason: " . socket_strerror(socket_last_error()));
            return $ret;
        }

        if (($r = socket_connect(
            $sock,
            'localhost',
            $this->_ports[$type]
        )) === false) {
            $this->_logger->err("AmuziSearch::autocomplete error on socket_connect $type. Reason: " . socket_strerror(socket_last_error($sock)));
            return $ret;
        }

        $c = new DZend_Chronometer();
        $c->start();
        socket_write($sock, $q, strlen($q));
        $str = socket_read($sock, 4096 * 1024, PHP_BINARY_READ);
        socket_close($sock);

        $resultList = "0\n" === $str ? array() : array_slice(explode("\n", $str), 0, 5 * $limit);
        foreach ($resultList as $result) {
            if (strlen($result) > 0) {
                list($artistName, $name) = explode('A', $result);
                $ret[] = new AutocompleteEntry($artistName, $name, null, $type);
                if (count($ret) >= $limit) {
                    break;
                }
            }
        }


        return $ret;
    }
}
