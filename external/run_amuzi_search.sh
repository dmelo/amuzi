#!/bin/bash
../library/amuzi_search/amuzi_search ../public/data/artist_album.txt 3675 >& ../public/tmp/amuzi_search_artist_album.log &
../library/amuzi_search/amuzi_search ../public/data/artist_track.txt 3676 >& ../public/tmp/amuzi_search_artist_track.log &
