<?php

/**
 * MusicTrackLink
 *
 * @package amuzi
 * @version 1.0
 * Amuzi - Online music
 * Copyright (C) 2010-2014  Diogo Oliveira de Melo
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
class MusicTrackLink extends DZend_Model
{
    private $_cache;
    private function _getCacheKey($artist, $musicTitle)
    {
        return sha1(
            'MusicTrackLinkID'
            . $this->_artistMusicTitleModel->insert($artist, $musicTitle)
        );
    }

    private function _getCacheIdKey($artistMusicTitleId)
    {
        return sha1('MusicTrackLinkID' . $artistMusicTitleId);
    }

    public function __construct()
    {
        parent::__construct();
        $this->_cache = Cache::get('cache');
    }

    public function bond($artistMusicTitleId, $trackId, $bondName)
    {
        try {
            $artistMusicTitleRow = $this->_artistMusicTitleDb->findRowById(
                $artistMusicTitleId
            );
            if (null === $artistMusicTitleRow) {
                try {
                    throw new Zend_Exception('ee');
                } catch (Zend_Exception $e) {
                    $this->_logger->err("MusicTrackLink::bond ($artistMusicTitleId, $trackId, $bondName) didn't find artistMusicTitleRow with id == $artistMusicTitleId --> " . $e->getTraceAsString());
                }
            }
            $artistRow = $this->_artistDb->findRowById(
                $artistMusicTitleRow->artistId
            );
            $musicTitleRow = $this->_musicTitleDb->findRowById(
                $artistMusicTitleRow->musicTitleId
            );
            $cacheKey = $this->_getCacheKey(
                $artistRow->name, $musicTitleRow->name
            );
            $cacheIdKey = $this->_getCacheIdKey($artistMusicTitleRow->id);

            $this->_cache->remove($cacheKey);
            $this->_cache->remove($cacheIdKey);
            $bondRow = $this->_bondModel->findRowByName($bondName);
            $currentMusicTrackLinkRow = $this->_musicTrackLinkDb->
                findRowByArtistMusicTitleIdAndTrackIdAndUserId(
                    $artistMusicTitleId,
                    $trackId,
                    null !== $this->_getUserRow() ?
                    $this->_getUserId() : null
                );

            $currentBondRow = null;
            if (null !== $currentMusicTrackLinkRow) {
                $currentBondRow = $this->_bondModel->findRowById(
                    $currentMusicTrackLinkRow->bondId
                );

                // If the current bond has higher priority than the new bond,
                // then don't do the new bond
                if($currentBondRow->priority > $bondRow->priority) {
                    return null;
                } else {
                    $currentMusicTrackLinkRow->delete();
                }
            }

            $data = array(
                'artist_music_title_id' => $artistMusicTitleId,
                'track_id' => $trackId,
                'user_id' => null !== $this->_getUserRow() ?
                    $this->_getUserId() : null,
                'bond_id' => $bondRow->id
            );

            return $this->_musicTrackLinkDb->insert($data);
        } catch (Exception $e) {
            return false;
        }
    }

    public function getTrackById($artistMusicTitleId, $sync = false)
    {
        $cacheKey = $this->_getCacheIdKey($artistMusicTitleId);
        if (false === ($ret = $this->_cache->load($cacheKey)) ||
            ($sync && null === $ret)) {
            // If it's not on cache, then insert it from artist,musicTitle, so
            // that all both caches are recorded.
            $artistMusicTitleRow = $this->_artistMusicTitleDb->findRowById(
                $artistMusicTitleId
            );

            return $this->_getTrackByAMTId(
                $artistMusicTitleId,
                $sync,
                $artistMusicTitleRow->getArtistName(),
                $artistMusicTitleRow->getMusicTitleName()
            );
        }

        return $ret;
    }

    public function getTrackByIdSync($artistMusicTitleId)
    {
        return $this->getTrackById($artistMusicTitleId, true);
    }

    protected function _getTrackByAMTId(
        $artistMusicTitleId, $sync, $artist, $musicTitle
    )
    {
        $cacheIdKey = $this->_getCacheIdKey($artistMusicTitleId);
        if (false === ($ret = $this->_cache->load($cacheIdKey))) {
            $rowSet = $this->_musicTrackLinkDb->findByArtistMusicTitleId(
                $artistMusicTitleId
            );
            if (count($rowSet) == 0 && $sync) {
                // Look for it on Youtube.
                $resultSet = $this->_youtubeModel->search(
                    "${artist} - ${musicTitle}", 5, 1, array(
                        'artist' => $artist,
                        'musicTitle' => $musicTitle
                    )
                );
                $this->_trackModel->insertMany(
                    $resultSet, $artist, $musicTitle
                );
                $rowSet = $this->_musicTrackLinkDb->findByArtistMusicTitleId(
                    $artistMusicTitleId
                );
            }
            $points = array();
            foreach ($rowSet as $row) {
                if (!array_key_exists($row->trackId, $points))
                    $points[$row->trackId] = 0;

                switch ($row->bondId) {
                    case 1: // search
                        $points[$row->trackId] += 1;
                        break;
                    case 2: // insert_playlist
                        $points[$row->trackId] += 4;
                        break;
                    case 3: // vote_up
                        $points[$row->trackId] += 16;
                        break;
                    case 4:
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

            $ret = 0 !== $trackId ?
                $this->_trackModel->findRowById($trackId) : null;

            if (null !== $ret) {
                $this->_cache->save($ret, $cacheIdKey);
            }
        }

        return $ret;
    }

    public function getTrack($artist, $musicTitle, $sync = false)
    {
        $c = new DZend_Chronometer();
        $c->start();

        $cacheKey = $this->_getCacheKey($artist, $musicTitle);
        $ret = null;

        if (false === ($ret = $this->_cache->load($cacheKey))) {
            $this->_logger->debug(
                "MusicTrackLink::getTrack cache miss $cacheKey"
            );
            $artistMusicTitleId = $this->_artistMusicTitleModel->insert(
                $artist, $musicTitle
            );
            $ret = $this->_getTrackByAMTId(
                $artistMusicTitleId, $sync, $artist, $musicTitle
            );

            if (null !== $ret) {
                $this->_cache->save($ret, $cacheKey);
            }
        } else {
            $this->_logger->debug(
                "MusicTrackLink::getTrack cache hit $cacheKey"
            );
        }
        $c->stop();
        $this->_logger->debug('MusicTrackLink::getTrack time ' . $c->get());

        return $ret;
    }

    public function getTrackSync($artist, $musicTitle)
    {
        return $this->getTrack($artist, $musicTitle, true);
    }
}
