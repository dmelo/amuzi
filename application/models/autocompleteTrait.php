<?php

trait autocompleteTrait
{
    /**
     * autocompleteTrait
     *
     * @param mixed $q
     * @param mixed $type It's either 'music_title' or 'album'
     * @param int $limit
     * @return void
     */
    public function acTrait($q, $type, $limit = 5)
    {
        $modelObj = 'music_title' === $type ? '_artistMusicTitleDb' : '_albumDb';
        $autocompleteType = 'music_title' === $type ? 'track' : 'album';
        $keywords = explode(' - ', $q, 2);
        $ret = array();
        if (count($keywords) === 1) {
            $ret = $this->$modelObj->autocomplete(
                array(
                    $type => $keywords[0]
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
                    $type => $keywords[1]
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
        }

        return array_slice($ret, 0, $limit);
    }
}
