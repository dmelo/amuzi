<?php

/**
 * 
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
trait autocompleteTrait
{
    /**
     * autocompleteTrait
     *
     * @param mixed $q Needle.
     * @param int $limit Will return up to $limit array elements.
     * @return array Returns an array of AutocompleteEntry instances.
     */
    public function autocomplete($q, $limit = 5)
    {
        $modelObj = 'music_title' === $this->_type ?
            '_artistMusicTitleDb' : '_albumDb';
        $autocompleteType = 'music_title' === $this->_type ? 'track' : 'album';

        $ret = $this->_amuziSearchModel->autocomplete(
            $q, $autocompleteType . '_db', $limit
        );
        // TODO: append info about pics on tracks.'

        if (count($ret) < $limit) {
            $ret = array_merge(
                $ret,
                $this->_amuziSearchModel->autocomplete(
                    $q, $autocompleteType, $limit - count($ret)
                )
            );
            $this->_taskRequestModel->addTask(
                'SearchString', 'music_title' === $autocompleteType ?
                'MusicTitle' : 'Album',
                $q
            );
        }

        return array_slice($ret, 0, $limit);
    }

    /**
     * getBestGuess Gets the best guess for the given string.
     *
     * @param string $q User's input string.
     * @return AutocompleteEntry Returns the fittest guess, or null it none is
     * found.
     */
    public function getBestGuess($q)
    {
        if (count($ret = $this->autocomplete($q, 1)) == 1) {
            return $ret[0];
        }
        return null;
    }
}
