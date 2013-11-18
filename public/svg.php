<?php
header("Content-type: image/svg+xml");
echo '<svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="560" height="440">
  <rect width="560" height="440"
    style="fill:rgb(' . $_GET['r'] . ',' . $_GET['g'] . ',' . $_GET['b'] . ');stroke-width:1;stroke:rgb(0,0,0)"/>
    </svg>';
