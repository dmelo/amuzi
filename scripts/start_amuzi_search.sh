#!/bin/bash

PID=`ps aux | grep amuzi_search | grep -e 3674 -e 3675 -e 3676 -e 3685 -e 3686 | grep -v grep | sed 's/  */ /g' | cut -d\  -f 2`
if ! [ $PID -n ]
then
    kill -9 $PID
fi



TYPES='artist album track'
SUFFIXES='.txt _r.txt'
PORT=3674


for SUFFIX in $SUFFIXES
do
    for TYPE in $TYPES
    do
        if ! [ '_r.txt' = $SUFFIX -a 'artist' = $TYPE ]
        then
            FILE="../../../public/data/artist_$TYPE$SUFFIX"
            DATAPATH=library/amuzi_search/env_$TYPE$SUFFIX-d/
	    mkdir -p $DATAPATH/data
            cd $DATAPATH $PORT
            if [[ $# -eq 1 && "$1" == 'debug' ]]
            then
                echo 'debug'
                xterm -fg white -bg black -e "echo \"r suffixarray $FILE $PORT\" | gdb ../amuzi_search" &
            else
                echo 'prod'
                ../amuzi_search suffixarray $FILE $PORT &
            fi
            cd -
        fi
        PORT=`expr $PORT + 1`
    done
    PORT=3684
done
