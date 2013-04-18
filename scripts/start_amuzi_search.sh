#!/bin/bash

killall -9 amuzi_search
cd library/amuzi_search/env_album/ ; ../amuzi_search suffixarray ../../../public/data/artist_album.txt 3675 &
cd -
cd library/amuzi_search/env_track/ ; ../amuzi_search suffixarray ../../../public/data/artist_track.txt 3676 &
