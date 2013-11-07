<?php

/**
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

$start = microtime(true);

require_once '../scripts/env.php';
require_once 'Zend/Config/Ini.php';


error_reporting(E_ALL);


$ports = array(
    'artist_db' =>   3671,
    'album_db' =>    3672,
    'track_db' =>    3673,

    'album_r_db' =>  3682,
    'track_r_db' =>  3683,



    'artist' =>      3674,
    'album' =>       3675,
    'track' =>       3676,

    'album_r' =>     3685,
    'track_r' =>     3686,
);


$pdo = null;

function getObjType($port)
{
    $type = null;
    switch ($port) {
        case 3671:
        case 3674:
            $type = 'artist';
            break;
        case 3672:
        case 3682:
        case 3675:
        case 3685:
            $type = 'album';
            break;
        case 3673:
        case 3683:
        case 3676:
        case 3686:
            $type = 'track';
            break;
    };

    if (null === $type) {
        logDebug("ERROR: on getObjType port == $port");
    }

    return $type;
}

function getPorts($type)
{
    $ports = array();
    switch ($type) {
        case 'artist':
            $ports = array(3671, 3674);
            break;
        case 'album':
            $ports = array(3672, 3682, 3675, 3685);
            break;
        case 'track':
            $ports = array(3673, 3683, 3676, 3686);
            break;
    };

    if (empty($ports)) {
        logDebug("ERROR: on getPorts type == $type");
    }

    return $ports;
}

function openSocket($port)
{
    $sock = false;
    if (($sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false) {
        echo "AmuziSearch::autocomplete error on socket_create. Reason: " . socket_strerror(socket_last_error()) . PHP_EOL;
        exit(1);
    }

    if (($r = socket_connect($sock, 'localhost', $port)) === false) {
        echo "AmuziSearch::autocomplete error on socket_connect $type, port $port. Reason: " . socket_strerror(socket_last_error($sock)) . PHP_EOL;
        exit(1);
    }

    return $sock;
}

function getResult($q, $port, $limit = 5)
{
    $sock = openSocket($port);

    socket_write($sock, $q, strlen($q));
    $str = '';
    while (($strAux = socket_read($sock, 4096 * 1024, PHP_BINARY_READ)) !== false && strlen($strAux) !== 0) {
        $str .= $strAux;
    }
    socket_shutdown($sock);
    socket_close($sock);
    $ret = array();

    if (false === $str) {
        echo "failed reading from socket" . PHP_EOL;
        exit(1);
    } else {
        $resultList = "0\n" === $str ? array() : explode("\n", $str);
        foreach ($resultList as $result) {
            if (strlen($result) > 0) {
                list($artistName, $name) = explode('A', $result);
                $artistName = ucfirst($artistName);
                $name = ucfirst($name);
                if ($port > 3680) {
                    $aux = $artistName;
                    $artistName = $name;
                    $name = $aux;
                }
                $ret[] = array(
                    'name' => '' === $name ? $artistName : "$artistName - $name",
                    'cover' => '/img/album.png',
                    'artist' => $artistName,
                    'musicTitle' => $name,
                    'type' => getObjType($port)
                );
                if (count($ret) >= $limit) {
                    break;
                }
            }
        }

        return $ret;
    }
}

function fillImages($sub, $type)
{
    try {
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
        if (null === $pdo) {
            $params = $config->resources->db->params;
            $dsn = 'mysql:dbname=' . $params->dbname  . ';host=' . $params->host;
            $user = $params->username;
            $password = $params->password;

            try {
                $pdo = new PDO($dsn, $user, $password);
            } catch (PDOException $e) {
                echo 'Connection failed: ' . $e->getMessage();
            }
        }

        $query = '';
        if ('album_db' === $type) {
            $query = 'select album.id as id, artist.name as artist, album.name as musicTitle, album.cover as cover from album join artist on album.artist_id = artist.id where ';
            $first = true;
            foreach ($sub as $r) {
                if ($first) {
                    $first = false;
                } else {
                    $query .= ' OR ';
                }
                $query .= '(artist.name = "' . $r['artist'] . '" AND album.name = "' . $r['musicTitle'] . '")';
            }
        } elseif ('track_db' === $type) {
            $query = 'select artist_music_title.id as id, artist.name as artist, music_title.name as musicTitle from artist_music_title join artist on artist_music_title.artist_id = artist.id join music_title on artist_music_title.music_title_id = music_title.id where ';
            $first = true;
            foreach ($sub as $r) {
                if ($first) {
                    $first = false;
                } else {
                    $query .= ' OR ';
                }

                $query .= '(artist.name = "' . $r['artist'] . '" AND music_title.name = "' . $r['musicTitle'] . '")';
            }
        }

        if ('' !== $query) {
            foreach ($pdo->query($query) as $row) {
                foreach ($sub as &$r) {
                    if (
                        strtolower($r['artist']) === strtolower($row[1])
                        && strtolower($r['musicTitle']) === strtolower($row[2])
                    ) {
                        $r['objId'] = 'album_db' === $type ? - ((int) $row[0]) : (int) $row[0];
                        $r['artist'] = $row[1];
                        $r['musicTitle'] = $row[2];
                        if (array_key_exists(3, $row)) {
                            $r['cover'] = $row[3];
                        }
                        $r['name'] = $row[1] . ' - ' . $row[2];
                    }
                }
            }
        }
    } catch (Zend_Exception $e) {
        echo $e->getMessage() . PHP_EOL;
    }

    return $sub;
}

function getEntries($q)
{
    $ret = array();
    if (strpos($q, ' - ') === false) {
        $words = explode(' ', $q);
        for ($i = 0; $i < count($words); $i++) {
            $w1 = implode(' ', array_slice($words, 0, $i));
            $w2 = implode(' ', array_slice($words, $i));
            $ret[] = "${w1}A${w2}";
            $ret[] = "${w2}A${w1}";
        }
        $ret[] = $q;
    } else {
        $pieces = explode(' - ', $q);
        $w1 = array_key_exists(0, $pieces) ? $pieces[0] : '';
        $w2 = array_key_exists(1, $pieces) ? $pieces[1] : '';
        $ret[] = "${w1}A${w2}";
        $ret[] = "${w2}A${w1}";
    }

    return $ret;
}

function logDebug($str, $q)
{
    $fd = fopen('tmp/log-php.txt', 'a');
    fwrite($fd, microtime(true) . ' q: ' . $q . ' - ' . $str . PHP_EOL);
    fclose($fd);
}

$q = urldecode($_GET['q']);
$logout = array_key_exists('logout', $_GET) && 'true' === $_GET['logout'];
logDebug("logout: " . print_r($logout, true));

$r = '/^ *$/';
if (preg_match($r, $q) || strlen($q) < 3) {
    return 0;
} else {
    logDebug('start', $q);
    $q = strtolower($q);
    $limit = 5;
    $dbLink = null;

    $ret = array();
    $types = $logout ?
        array('artist', 'album'):
        array('album', 'track');

    foreach ($types as $type) {
        $sub = array();
        foreach (getPorts($type) as $port) {
            $aux = array();
            foreach (getEntries($q, $type) as $entry) {
                if (count($sub) < $limit) {
                    $aux = array_merge($aux, getResult($entry, $port));
                }
            }
            if (in_array($port, array(3672, 3682))) {
                $aux = fillImages($aux, 'album_db');
            }
            foreach ($aux as $newResult) {
                $isUnique = true;
                foreach ($sub as $result) {
                    if (strtoupper($result['name']) === strtoupper($newResult['name'])) {
                        $isUnique = false;
                        break;
                    }
                }
                if ($isUnique && count($sub) < $limit) {
                    $sub[] = $newResult;
                }
            }
        }
        $ret = array_merge($ret, $sub);
    }

    logDebug('end: ' . count($ret) . ' results', $q);
    logDebug('ret: ' . print_r($ret, true));
    echo json_encode($ret);
}

$end = microtime(true);
logDebug("Total time: " . ($end - $start));
