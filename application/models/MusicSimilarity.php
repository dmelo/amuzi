<?php

class MusicSimilarity extends DZend_Model
{
    public function insert(
        $artistMusicTitleId, $artistMusicTitleId2, $similarity
    )
    {
        list($i1, $i2) = $artistMusicTitleId < $artistMusicTitleId2 ?
            array($artistMusicTitleId, $artistMusicTitleId2):
            array($artistMusicTitleId2, $artistMusicTitleId);
        return $this->_musicSimilarityDb->insert(
            array('f_artist_music_title_id' => $i1,
                's_artist_music_title_id' => $i2,
                'similarity' => $similarity
            )
        );
    }
}
