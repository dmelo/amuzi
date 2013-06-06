#!/bin/bash

cd library/amuzi_search/env_album/

if [[ $# -eq 1 && "$1" == 'debug' ]]
then
    echo 'debug'
    xterm -fg white -bg black -e 'echo "r suffixarray ../../../public/data/artist_album.txt 3675" | gdb ../amuzi_search' &
else
    echo 'prod'
    ../amuzi_search suffixarray ../../../public/data/artist_album.txt 3675 &
fi
cd -

cd library/amuzi_search/env_track/

if [[ $# -eq 1 && $1 = 'debug' ]]
then
    echo 'debug'
    xterm -fg white -bg black -e 'echo "r suffixarray ../../../public/data/artist_track.txt 3676" | gdb ../amuzi_search' &
else
    echo 'prod'
    ../amuzi_search suffixarray ../../../public/data/artist_track.txt 3676 &
fi
