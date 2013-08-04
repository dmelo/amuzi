#!/bin/bash

PID=`ps aux | grep amuzi_search | grep -e 3673 -e 3672 -e 3671 -e 3682 -e 3683 | grep -v grep | sed 's/  */ /g' | cut -d\  -f 2`
if ! [ $PID -n ]
then
    kill -9 $PID
fi

TYPES='artist album track'
SUFFIXES='.txt _r.txt'
PORT=3671

for SUFFIX in $SUFFIXES
do
    for TYPE in $TYPES
    do
        rm /tmp/artist_${TYPE}_db.txt

        SELECT=''
        case $TYPE in
            'artist' )
                SELECT="select lower(name) from artist"
                ;;
            'album' )
                if [ '.txt' = $SUFFIX ]
                then
                    COLUMNS="lower(artist.name), 'A', lower(album.name)"
                else
                    COLUMNS="lower(album.name), 'A', lower(artist.name)"
                fi
                SELECT="select concat($COLUMNS) from artist join album on artist.id = album.artist_id"
                ;;
            'track' )
                if [ '.txt' = $SUFFIX ]
                then
                    COLUMNS="lower(artist.name), 'A', lower(music_title.name)"
                else
                    COLUMNS="lower(music_title.name), 'A', lower(artist.name)"
                fi

                SELECT="select concat($COLUMNS) from artist join artist_music_title on artist.id = artist_music_title.artist_id join music_title on artist_music_title.music_title_id = music_title.id"
                ;;
        esac


        if ! [ 'artist' = $TYPE -a '_r.txt' = $SUFFIX ]
        then
            echo $SELECT | mysql -u root -pcafess123 youbetter > /tmp/artist_${TYPE}_db${SUFFIX}.tmp
            tail -n +3 /tmp/artist_${TYPE}_db.txt.tmp > /tmp/artist_${TYPE}_db${SUFFIX}
            cd library/amuzi_search/env_${TYPE}_db${SUFFIX}-d/

            rm -rf data/*

            FILE="/tmp/artist_${TYPE}_db${SUFFIX}"

            if [[ $# -eq 1 && $1 = 'debug' ]]
            then
                echo debug
                xterm -fg white -bg black -e "echo \"r suffixarray $FILE $PORT\" | gdb ../amuzi_search" &
            else
                echo prod
                ../amuzi_search suffixarray $FILE $PORT &
            fi

            cd -
        fi

        PORT=`expr $PORT + 1`
    done
    PORT=3681
done
