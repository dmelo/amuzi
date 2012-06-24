<?php

class ArtistMusicTitle
{
    protected $_artistMusicTitleDb;
    protected $_artistModel;
    protected $_musicTitleModel;

    public function __construct()
    {
        $this->_artistMusicTitleDb = new DbTable_ArtistMusicTitle();
        $this->_artistModel = new Artist();
        $this->_musicTitleModel = new MusicTitle();
    }

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
