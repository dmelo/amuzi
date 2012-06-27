<?php

class MusicTrackLink
{
    protected $_musicTrackLinkDb;
    protected $_bondModel;
    protected $_session;

    public function __construct()
    {
        $this->_musicTrackLinkDb = new DbTable_MusicTrackLink();
        $this->_bondModel = new Bond();
        $this->_session = DZend_Session_Namespace::get('session');
    }

    public function bond($artistMusicTitleId, $trackId, $bondName)
    {
        try {
            $bondRow = $this->_bondModel->findRowByName($bondName);
            $currentMusicTrackLinkRow = $this->_musicTrackLinkDb->
                findRowByArtistMusicTitleIdAndTrackIdAndUserId(
                    $artistMusicTitleId,
                    $trackId,
                    $this->_session->user->id
                );

            $currentBondRow = null;
            if (null !== $currentMusicTrackLinkRow) {
                $currentBondRow = $this->_bondModel->findRowById($currentMusicTrackLinkRow->bondId);

                // If the current bond has higher priority than the new bond,
                // then don't do the new bond
                if($currentBondRow->priority > $bondRow->priority)
                    return null;
            }


            return $this->_musicTrackLinkDb->insert(array(
                'artist_music_title_id' => $artistMusicTitleId,
                'track_id' => $trackId,
                'user_id' => $this->_session->user->id,
                'bond_id' => $bondRow->id
            ));
        } catch (Exception $e) {
            return null;
        }
    }
}
