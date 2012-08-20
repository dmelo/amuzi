<?php

class ArtistMusicTitle extends DZend_Model
{
    public function insert($artist, $musicTitle)
    {
        $artistId = $this->_artistModel->insert($artist);
        $this->_logger->debug('ArtistMusicTitle::insert artist -> ' . $artistId);
        $musicTitleId = $this->_musicTitleModel->insert($musicTitle);
        $this->_logger->debug('ArtistMusicTitle::insert musicTitle -> ' . $musicTitleId);

        return $this->_artistMusicTitleDb->insert(
            array(
                'artist_id' => $artistId,
                'music_title_id' => $musicTitleId
            )
        );
    }

    public function findByArtistAndMusicTitle($artist, $musicTitle)
    {
        $db = $this->_artistMusicTitleDb->getAdapter();
        $where = $db->quoteInto('artist_id in (select id from artist where name = ?)', $artist);
        $where .= $db->quoteInto(' AND music_title_id in (select id from music_title where name = ?)', $musicTitle);
        return $this->_artistMusicTitleDb->fetchRow($where);
    }
}
