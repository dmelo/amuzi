<?php

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

require_once 'Zend/Config/Ini.php';


error_reporting(E_ALL);


$ports = array(
    'album_db' => 3673, 'track_db' => 3674, 'album' => 3675, 'track' => 3676
);
$pdo = null;

function openSocket($port)
{
    $sock = false;
    if (($sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false) {
        echo "AmuziSearch::autocomplete error on socket_create. Reason: " . socket_strerror(socket_last_error()) . PHP_EOL;
        exit(1);
    }

    if (($r = socket_connect($sock, 'localhost', $port)) === false) {
        echo "AmuziSearch::autocomplete error on socket_connect $type. Reason: " . socket_strerror(socket_last_error($sock)) . PHP_EOL;
        exit(1);
    }

    return $sock;
}

function getResult($q, $port, $limit = 5)
{
    $sock = openSocket($port);

    socket_write($sock, $q, strlen($q));
    $str = socket_read($sock, 4096 * 1024, PHP_BINARY_READ);
    socket_shutdown($sock);
    socket_close($sock);
    $ret = array();

    if (false === $str) {
        echo "failed reading from socket" . PHP_EOL;
        exit(1);
    } else {
        $resultList = "0\n" === $str ? array() : array_slice(explode("\n", $str), 0, 5 * $limit);
        foreach ($resultList as $result) {
            if (strlen($result) > 0) {
                list($artistName, $name) = explode('A', $result);
                $artistName = $artistName;
                $name = $name;
                $ret[] = array(
                    'name' => "$artistName - $name",
                    'cover' => '/img/album.png',
                    'artist' => $artistName,
                    'musicTitle' => $name,
                    'type' => $port % 2 ? 'album': 'track'
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
                        $r['artist'] === strtolower($row[1])
                        && $r['musicTitle'] === strtolower($row[2])
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

function logDebug($str, $q)
{
    $fd = fopen('tmp/log-php.txt', 'a');
    fwrite($fd, microtime(true) . ' q: ' . $q . ' - ' . $str . PHP_EOL);
    fclose($fd);
}

$q = urldecode($_GET['q']);

$r = '/^ *$/';
if (preg_match($r, $q) || strlen($q) < 3) {
    return 0;
} else {
    logDebug('start', $q);
    $q = str_replace(' - ', 'A', strtolower($q));
    $limit = 5;
    $dbLink = null;

    $ret = array();
    foreach (array('album_db', 'track_db') as $type) {
        $sub = getResult($q, $ports[$type]);
        if (count($sub) > 0) {
            $sub = fillImages($sub, $type);
        }
        if (count($sub) < $limit) {
            $complemet = getResult($q, $ports[str_replace('_db', '', $type)], $limit - count($sub));
            $sub = array_merge($sub, $complemet);
        }

        $ret[] = $sub;
    }

    $ret = array_merge($ret[0], $ret[1]);
    logDebug('end: ' . count($ret) . ' results', $q);
    echo json_encode($ret);
}
