<?php

class AmuziSearch extends DZend_Model
{
    protected $_ports = array(
        'album' => 3675, 'track' => 3676, 'artist' => 3677
    );
    protected $_keys = array('album', 'track', 'artist');

    public function autocomplete($q, $type)
    {
        if (!in_array($type, $this->_keys)) {
            $this->_logger->debug("AmuziSearch::autocomplete wrong type $type.");
            return false;
        }

        $this->_session = DZend_Session_Namespace::get('session');
        // TODO: check if socket is still alive.
        if (!isset($this->_session->searchSocket)) {
            $this->_session->searchSocket = array();
            if (($r = $this->_session->searchSocket[$type] = socket_create(
                AF_INET, SOCK_STREAM, SOL_TCP
            )) === false) {
                $this->_logger->error("AmuziSearch::autocomplete error on socket_create. Reason: " . socket_strerror(socket_last_error()));
                return false;
            }

            if (($r = socket_connect(
                $this->_session->searchSocket[$type],
                'localhost',
                $this->_port[$type]
            )) === false) {
                $this->_logger->error("AmuziSearch::autocomplete error on socket_connect $type. Reason: " . socket_strerror(socket_last_error($this->_session->searchSocket[$type])));
                return false;
            }
        }

        socket_write($this->_session->searchSocket[$type], $q, strlen($q));
        socket_read($this->_session->searchSocket[$type], $out, 512 * 1024);

        $this->_logger->debug("AmuziSearch::autocomplete result: " . $out);

        return true;
    }
}
