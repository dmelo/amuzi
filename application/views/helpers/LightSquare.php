<?php

require_once('views/helpers/T.php');
class View_Helper_LightSquare extends View_Helper_T
{
    public function lightSquare($href, $name, $img)
    {
    return '<div class="item-square playlist-square object-playlist" >'
            . '<div class="cover"><a href="' . $href . '"><img src="' . $img
            . '"/></a></div>' . '<div class="name">' . ucfirst($name)
            . '</div>'
            . '</div>';
    }
}
