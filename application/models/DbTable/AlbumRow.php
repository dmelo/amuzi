<?php

/**
 * DbTable_AlbumRow
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
class DbTable_AlbumRow extends DZend_Db_Table_Row
    implements DbTable_iTrackCollectionRow
{
    protected $_trackList = null;
    protected $_artistRow = null;

    protected function _getArtistRow()
    {
        if (null === $this->_artistRow) {
            $artistDb = new DbTable_Artist();
            $this->_artistRow = $artistDb->findRowById($this->artistId);
        }

        return $this->_artistRow;
    }

    public function getArray()
    {
        $columns = array(
            'id',
            'title',
            'name',
            'cover',
            'artist',
            'trackList',
            'shareUrl'
        );

        $ret = array();
        foreach ($columns as $column) {
            if ('cover' === $column) {
                $ret[$column] = $this->getCover();
            } else {
                $ret[$column] = $this->$column;
            }
        }

        return $ret;
    }

    public function getTrackListAsArray($sync = false)
    {
        $c = new DZend_Chronometer();
        $c->start();

        if (null === $this->_trackList) {
            $this->_trackList = array(false => null, true => null);
        }

        if (($ret = $this->_trackList[$sync]) === null) {
            $ret = array();
            $artistMusicTitleModel = new ArtistMusicTitle();
            $musicTrackLinkModel = new MusicTrackLink();

            foreach ($this->artistMusicTitleIdList as $artistMusicTitleId) {
                $trackRow = $musicTrackLinkModel->getTrackById(
                    $artistMusicTitleId, $sync
                );
                $artistMusicTitleRow = $artistMusicTitleModel->findRowById(
                    $artistMusicTitleId
                );
                if (null === $trackRow) {
                    $track = array(
                        'artist' => $artistMusicTitleRow->getArtistName(),
                        'musicTitle' => $artistMusicTitleRow
                            ->getMusicTitleName()
                    );
                } else {
                    $track = $trackRow->getArray();
                    $track['title'] = $artistMusicTitleRow->getArtistName()
                        . ' - ' . $artistMusicTitleRow->getMusicTitleName();
                    $track['artist_music_title_id'] = $artistMusicTitleId;
                }

                $ret[] = $track;
            }

            $this->_trackList[$sync] = $ret;
        }

        $c->stop();
        $this->_logger->debug(
            "AlbumRow::getTrackListAsArray sync = "
            . ($sync ? 'true' : 'false') . " " . $c->get()
        );

        return $ret;
    }

    public function getTrackListSync()
    {
        return $this->getTrackListAsArray(true);
    }

    public function playTime()
    {
        $time = 0;
        foreach ($this->trackList as $trackRow) {
            if (array_key_exists('duration', $trackRow)) {
                $time += $trackRow['duration'];
            }
        }

        return $time;
    }

    public function getCover()
    {
        return in_array($this->cover, array(null, '')) ?
            '/img/album.png' : $this->cover;
    }

    public function getType()
    {
        return 'album';
    }

    public function getCoverName()
    {
        return $this->name;
    }

    public function getArtistRow()
    {
        return $this->_getArtistRow();
    }

    public function __get($name)
    {

        if ('artist' === $name) {
            return $this->_getArtistRow()->name;
        } elseif ('title' === $name) {
            return "{$this->artist} - {$this->name}";
        } elseif ('artistMusicTitleIdList' === $name) {
            $albumHasArtistMusicTitleDb =
                new DbTable_AlbumHasArtistMusicTitle();
            $ret = array();
            $ahamtRowset = $albumHasArtistMusicTitleDb
                ->findByAlbumId($this->id);
            foreach ($ahamtRowset as $row) {
                $ret[] = $row->artistMusicTitleId;
            }

            return $ret;
        } elseif ('trackList' === $name) {
            return $this->getTrackListAsArray(); // Assynchronous.
        } elseif ('shareUrl' === $name || 'share_url' === $name) {
            $domain = Zend_Registry::get('domain');
            return $domain . '/share/index/command/a/param/' . $this->id;
        } elseif ('facebookUrl' === $name) {
            return 'http://facebook.com/share.php?u='
                . urlencode($this->shareUrl);
        } elseif ('pageUrl' === $name) {
            $domain = Zend_Registry::get('domain');
            return $domain . '/album/' . urlencode($this->artist) . '/'
                . urlencode($this->name);
        } else {
            return parent::__get($name);
        }
    }
}
