<?php

require_once('views/helpers/T.php');
class View_Helper_ArtistSquare extends View_Helper_T
{
    public function artistSquare(DbTable_ArtistRow $artistRow)
    {
    return '<div class="item-square playlist-square object-playlist"'
            . ' id="' . $artistRow->id . '">'
            . '<div class="cover"><a href="/artist/' . urlencode($artistRow->name) . '"><img src="' . $artistRow->getCover()
            . '"/></a></div>' . '<div class="name">' . ucfirst($artistRow->name)
            . '</div>'
            . '</div>';
    }
}
