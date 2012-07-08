<?php

class MusicTrackLink extends DZend_Model
{
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
                else
                    $currentMusicTrackLinkRow->delete();
            }

            $data = array(
                'artist_music_title_id' => $artistMusicTitleId,
                'track_id' => $trackId,
                'user_id' => $this->_session->user->id,
                'bond_id' => $bondRow->id
            );

            return $this->_musicTrackLinkDb->insert($data);
        } catch (Exception $e) {
            return null;
        }
    }

    public function getTrack($artist, $musicTitle)
    {
        $artistMusicTitleId = $this->_artistMusicTitleModel->insert($artist, $musicTitle);
        $rowSet = $this->_musicTrackLinkDb->findByArtistMusicTitleId($artistMusicTitleId);
        $points = array();
        foreach ($rowSet as $row) {
            if (!array_key_exists($row->trackId, $points))
                $points[$row->trackId] = 0;

            switch ($row->bond_id) {
                case 0: // search
                    $points[$row->trackId] += 1;
                    break;
                case 1: // insert_playlist
                    $points[$row->trackId] += 4;
                    break;
                case 2: // vote_up
                    $points[$row->trackId] += 16;
                    break;
                case 3:
                    $points[$row->trackId] -= 16;
                    break;
            }
        }

        $trackId = 0;
        $maxPoints = -10000;
        foreach ($points as $key => $value) {
            if ($maxPoints < $value) {
                $maxPoints = $value;
                $trackId = $key;
            }
        }

        if ($trackId !== 0)
            return $this->_trackModel->findRowById($trackId);
        else
            return null;
    }
}
