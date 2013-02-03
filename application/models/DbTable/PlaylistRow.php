<?php

/**
 * DbTable_PlaylistRow
 *
 * @package Amuzi
 * @version 1.0
 * Amuzi - Online music
 * Copyright (C) 2010-2013  Diogo Oliveira de Melo
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
class DbTable_PlaylistRow extends DZend_Db_Table_Row implements DbTable_iTrackCollectionRow
{
    protected $_playlistDb;
    protected $_playlistHasTrackDb;
    protected $_trackDb;

    public function __construct(array $config = array())
    {
        parent::__construct($config);
        $this->_playlistDb = new DbTable_Playlist();
        $this->_playlistHasTrackDb = new DbTable_PlaylistHasTrack();
        $this->_trackDb = new DbTable_Track();
    }
    public function setTrack($trackInfo, $sort)
    {
        // Set the right order and bound between the track and the playlist.
        $data = array(
            'playlist_id' => $this->id,
            'track_id' => $trackInfo['id'],
            'sort' => $sort);

        $this->_playlistHasTrackDb->insert($data);
    }

    /**
     * addTrack Add a track to this playlist.
     *
     * @param array $trackInfo is a array that have either the keys title, mp3
     * and cover or the key id. The latter is valid just in case the track
     * already exists
     * @return Returns the track row.
     */
    public function addTrack($trackInfo)
    {
        $maxSort = $this->_playlistHasTrackDb->getMaxSort($this->id);
        if (array_key_exists('id', $trackInfo))
            $trackRow = $this->_trackDb->findRowById($trackInfo['id']);
        else
            $trackRow = $this->_trackDb->insert($trackInfo);

        if (null === $trackRow) {
            throw new Zend_Db_Table_Row_Exception(
                'Could not find the track: ' . print_r($trackInfo, true)
            );
        } else {
            $data = array(
                'playlist_id' => $this->id,
                'track_id' => $trackRow->id,
                'sort' => $maxSort + 1);
            if (array_key_exists('artist_music_title_id', $trackInfo))
                $data['artist_music_title_id']
                    = $trackInfo['artist_music_title_id'];
            $this->_playlistHasTrackDb->insert($data);

            return $trackRow;
        }
    }

    public function rmTrack($trackId)
    {
        $trackRow = $this->_trackDb->findRowById($trackId);
        $this->_playlistHasTrackDb->deleteByPlaylistIdAndTrackId(
            $this->id,
            $trackRow->id
        );
    }

    public function deleteSortGreaterThan($sort)
    {
        $this->_playlistHasTrackDb->deleteByPlaylistSortGreaterThan(
            $this->id,
            $sort
        );
    }

    public function getTrackListAsArray()
    {
        $list = $this->_playlistHasTrackDb->findByPlaylistId($this->id);
        $ret = array();
        foreach ($list as $item) {
            $ret[] = array_merge(
                $this->_trackDb->findRowById($item->trackId)->getArray(),
                array('artist_music_title_id' => $item->artistMusicTitleId)
            );
        }

        return $ret;
    }

    public function playTime()
    {
        $time = 0;
        foreach ($this->getTrackListAsArray() as $trackRow) {
            $time += $trackRow['duration'];
        }

        return $time;
    }

    public function countTracks()
    {
        return count($this->_playlistHasTrackDb->findByPlaylistId($this->id));
    }

    public function getCover()
    {
        $select = $this->_trackDb->select()->from(
            array('p' => 'playlist'), array()
        );
        $select->join(
            array('pht' => 'playlist_has_track'),
            'p.id = pht.playlist_id',
            array()
        );
        $select->join(
            array('t' => 'track'), 't.id = pht.track_id', array('cover')
        );
        $select->where(
            $this->_playlistDb->getAdapter()->quoteInto(
                't.cover is not null and p.id = ?',
                $this->id
            )
        );

        $row = $this->_playlistDb->fetchRow($select);
        return $row ? $row->cover : '/img/playlist64.png';
    }

    public function getType()
    {
        return 'playlist';
    }
}
