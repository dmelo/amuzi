#!/bin/bash

killall -9 amuzi_search
cd library/amuzi_search
./amuzi_search bmh ../../public/data/artist_album.txt 3675 &
./amuzi_search bmh ../../public/data/artist_track.txt 3676 &
