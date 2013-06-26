#!/bin/bash


TYPES='artist album track'
PORT=3674

for TYPE in $TYPES
do
    cd library/amuzi_search/env_$TYPE/
    if [[ $# -eq 1 && "$1" == 'debug' ]]
    then
        echo 'debug'
        xterm -fg white -bg black -e "echo \"r suffixarray ../../../public/data/artist_$TYPE.txt $PORT\" | gdb ../amuzi_search" &
    else
        echo 'prod'
        ../amuzi_search suffixarray ../../../public/data/artist_$TYPE.txt $PORT &
    fi
    cd -
    PORT=`expr $PORT + 1`
done
