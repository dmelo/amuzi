<?php

/**
 * DbTable_AlbumHasArtistMusicTitle
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
class DbTable_AlbumHasArtistMusicTitle extends DZend_Db_Table
{
    public function fetchAllByArtistMusicTitleIdGrouped($artistMusicTitleIdList)
    {
        $select = $this->select()
            ->where(
                'artist_music_title_id in ('
                . implode(', ', $artistMusicTitleIdList) . ')'
            )->group('artist_music_title_id');
        return $this->fetchAll($select);
    }

    public function fetchAllAMTIdsFromAlbumIdList($albumIdList)
    {
        $ret = array();
        if (!empty($albumIdList)) {
            $select = $this->select()
                ->where('album_id in (' . implode(', ', $albumIdList) . ')')
                ->group('artist_music_title_id');
            $rowSet = $this->fetchAll($select);
            foreach ($rowSet as $row) {
                $ret[] = $row->artistMusicTitleId;
            }
        }

        return $ret;
    }
}
