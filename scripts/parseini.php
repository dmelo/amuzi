<?php

/**
 * 
 *
 * @package Amuzi
 * @version 1.0
 * Amuzi - Online music
 * Copyright (C) 2010-2014  Diogo Oliveira de Melo
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
function parse_ini_file_extended($filename) {
    $p_ini = parse_ini_file($filename, true);
    $config = array();
    foreach($p_ini as $namespace => $properties){
        list($name, $extends) = explode(':', $namespace);
        $name = trim($name);
        $extends = trim($extends);
        // create namespace if necessary
        if(!isset($config[$name])) $config[$name] = array();
        // inherit base namespace
        if(isset($p_ini[$extends])){
            foreach($p_ini[$extends] as $prop => $val)
                $config[$name][$prop] = $val;
        }
        // overwrite / set current namespace values
        foreach($properties as $prop => $val)
        $config[$name][$prop] = $val;
    }
    return $config;
}

if (count($argv) !== 4) {
    echo "Usage: parseini.php file.ini section var" . PHP_EOL;
} else {
    $ini = parse_ini_file_extended($argv[1]);

    echo $ini[$argv[2]][$argv[3]] . PHP_EOL;
}
