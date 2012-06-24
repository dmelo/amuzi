<?php

class MusicTrackLink
{
    protected $_musicTrackLinkDb;
    protected $_session;

    public function __construct()
    {
        $this->_musicTrackLinkDb = new DbTable_MusicTrackLink();
        $this->_session = DZend_Session_Namespace::get('session');
    }

    public function vote($artistMusicTitleId, $trackId, $vote)
    {
        return $this->_musicTrackLinkDb->insert(array(
            'artist_music_title_id' => $artistMusicTitleId,
            'track_id' => $trackId,
            'user_id' => $this->_session->user->id,
            'vote' => $vote
        ));
    }

    public function voteBlank($artistMusicTitleId, $trackId)
    {
        return $this->vote($artistMusicTitleId, $trackId, 0);
    }
}
