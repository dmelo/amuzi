<?php

class Form_PlaylistSettings extends Form_Search
{
    public function init()
    {
        $this->_placeholder = 'Playlist name...';
        parent::init();
        $this->setAction('/playlist/search');
        $this->setAttrib('id', 'playlistsettings');
        $this->setMethod('post');
    }
}
