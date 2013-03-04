<?php

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
        $modelObj = 'music_title' === $this->_type ? '_artistMusicTitleDb' : '_albumDb';
        $autocompleteType = 'music_title' === $this->_type ? 'track' : 'album';
        $keywords = explode(' - ', $q, 2);
        $ret = array();
        $this->_logger->debug("---> " . $this->_type);
        if (count($keywords) === 1) {
            $ret = $this->$modelObj->autocomplete(
                array(
                    $this->_type => $keywords[0]
                )
            );
            if (count($ret) < $limit) {
                $ret = array_merge($ret, $this->$modelObj->autocomplete(
                    array(
                        'artist' => $keywords[0]
                    )
                ));
            }
        } elseif (count($keywords) === 2) {
            $ret = $this->$modelObj->autocomplete(
                array(
                    'artist' => $keywords[0],
                    $this->_type => $keywords[1]
                )
            );
        }

        if (count($ret) < $limit) {
            $ret = array_merge(
                $ret,
                $this->_amuziSearchModel->autocomplete(
                    $q, $autocompleteType, $limit - count($ret)
                )
            );
            $this->_taskRequestModel->addTask('SearchString', 'music_title' === $autocompleteType ? 'MusicTitle' : 'Album', $q);
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
