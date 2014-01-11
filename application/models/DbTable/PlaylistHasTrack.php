<?php

/**
 * DbTable_PlaylistHasTrack
 *
 * @package Amuzi
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
class DbTable_PlaylistHasTrack extends DZend_Db_Table
{
    public function insert($data)
    {
        $row = $this->findRowByPlaylistIdAndSort(
            $data['playlist_id'],
            $data['sort']
        );
        if (!$row) {
            parent::insert($data);
            $row = $this->findRowByPlaylistIdAndSort(
                $data['playlist_id'],
                $data['sort']
            );
        } elseif ($row->trackId != $data['track_id']) {
            $row->trackId = $data['track_id'];
            $row->save();
        }

        return $row;
    }

    public function deleteByPlaylistSortGreaterThan($playlistId, $sort)
    {
        $where = $this->_db->quoteInto("playlist_id = ? AND ", $playlistId);
        $where .= $this->_db->quoteInto("sort > ?", $sort);
        $this->delete($where);
    }

    public function deleteByPlaylistIdAndTrackId($playlistId, $trackId)
    {
        $row = $this->findRowByPlaylistIdAndTrackId($playlistId, $trackId);
        $sort = $row->sort;
        $row->delete();

        // TODO: try to optimize this to something like update set sort = sort
        // - 1 where sort > $sort. It works on the mysql console but not on
        // Zend.
        for ($i = $sort + 1; $i <= $this->getMaxSort($playlistId); $i++) {
            $set = array('sort' => $i - 1);
            $where = $this->_db->quoteInto('playlist_id = ?', $playlistId);
            $where .= $this->_db->quoteInto(' AND sort = ?', $i);
            $this->update($set, $where);
        }
    }

    /**
     * getMaxSort Get the highiest sort on a playlist.
     *
     * @param mixed $playlistId Playlist ID;
     * @return int the max sort.
     */
    public function getMaxSort($playlistId)
    {
        $select = $this->select()->from(
            'playlist_has_track',
            array('max(sort) as max', 'count(*) as count')
        );

        $select->where($this->_db->quoteInto('playlist_id = ?', $playlistId));

        $row = $this->fetchRow($select);
        if ($row->count)
            return $row->max;
        else
            return -1;
    }
}
