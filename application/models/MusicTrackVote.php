<?php

class MusicTrackVote
{
    protected $_musicTrackVoteDb;
    protected $_session;

    public function __construct()
    {
        $this->_musicTrackVoteDb = new DbTable_MusicTrackVote();
        $this->_session = DZend_Session_Namespace::get('session');
    }

    public function vote($artistMusicTitleId, $trackId, $vote)
    {
        return $this->_musicTrackVoteDb->insert(array(
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
