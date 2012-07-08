<?php

class ArtistMusicTitle extends DZend_Model
{
    public function insert($artist, $musicTitle)
    {
        $artistId = $this->_artistModel->insert($artist);
        $musicTitleId = $this->_musicTitleModel->insert($musicTitle);

        return $this->_artistMusicTitleDb->insert(
            array(
                'artist_id' => $artistId,
                'music_title_id' => $musicTitleId
            )
        );
    }
}
