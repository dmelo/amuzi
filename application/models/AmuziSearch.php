<?php

/**
 * AmuziSearch
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
class AmuziSearch extends DZend_Model
{
    protected $_ports = array(
        'album_db' => 3673, 'track_db' => 3674, 'album' => 3675, 'track' => 3676
    );
    protected $_keys = null;

    public function autocomplete($q, $type, $limit = 5)
    {
        $typeRet = str_replace('_db', '', $type);
        if (null === $this->_keys) {
            $this->_keys = array_keys($this->_ports);
        }

        $ret = array();
        $q = preg_replace('/ - ?/', 'A', strtolower($q));
        ob_implicit_flush();
        if (!in_array($type, $this->_keys)) {
            $this->_logger->debug(
                "AmuziSearch::autocomplete wrong type $type."
            );
            return $ret;
        }

        $varName = 'amuziSearch' . $this->_ports[$type];
        $sock = false;
        if (($sock = socket_create(
            AF_INET, SOCK_STREAM, SOL_TCP
        )) === false) {
            $this->_logger->err(
                "AmuziSearch::autocomplete error on socket_create. Reason: "
                . socket_strerror(socket_last_error())
            );
            return $ret;
        }

        if (($r = socket_connect(
            $sock,
            'localhost',
            $this->_ports[$type]
        )) === false) {
            $this->_logger->err(
                "AmuziSearch::autocomplete error on socket_connect $type. "
                . "Reason: " . socket_strerror(socket_last_error($sock))
            );
            return $ret;
        }

        $c = new DZend_Chronometer();
        $c->start();
        false === socket_write($sock, $q, strlen($q));
        $str = socket_read($sock, 4096 * 1024, PHP_BINARY_READ);
        socket_shutdown($sock);
        socket_close($sock);

        if (false === $str) {
            $this->_logger->debug(
                'AmuziSearch::autocomplete failed to read from socket. '
                . 'try to open a new connection'
            );
        } else {
            $resultList = "0\n" === $str ?
                array() : array_slice(explode("\n", $str), 0, 5 * $limit);
            foreach ($resultList as $result) {
                if (strlen($result) > 0) {
                    list($artistName, $name) = explode('A', $result);
                    $ret[] = new AutocompleteEntry(
                        $artistName, $name, null, $typeRet
                    );
                    if (count($ret) >= $limit) {
                        break;
                    }
                }
            }
        }

        return $ret;
    }
}
