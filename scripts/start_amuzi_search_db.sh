#!/bin/bash

PID=`ps aux | grep amuzi_search | grep -e 3673 -e 3672 -e 3671 | grep -v grep | sed 's/  */ /g' | cut -d\  -f 2`
kill -9 $PID

TYPES='artist album track'
PORT=3671

for TYPE in $TYPES
    do
    rm /tmp/artist_${TYPE}_db.txt

    SELECT=''
    if [ 'artist' = $TYPE ]
    then
        SELECT="select lower(name) from artist"
    else
        if [ 'album' = $TYPE ]
        then
            SELECT="select concat(lower(artist.name), 'A', lower(album.name)) from artist join album on artist.id = album.artist_id"
        else
            if [ 'track' = $TYPE ]
            then
                SELECT="select concat(lower(artist.name), 'A', lower(music_title.name)) from artist join artist_music_title on artist.id = artist_music_title.artist_id join music_title on artist_music_title.music_title_id = music_title.id"
            fi
        fi
    fi

    echo $SELECT | mysql -u root -pcafess123 youbetter > /tmp/artist_${TYPE}_db.txt.tmp
    tail -n +3 /tmp/artist_${TYPE}_db.txt.tmp > /tmp/artist_${TYPE}_db.txt
    cd library/amuzi_search/env_${TYPE}_db/

    rm -rf data/*

    if [[ $# -eq 1 && $1 = 'debug' ]]
    then
        echo debug
        xterm -fg white -bg black -e "echo \"r suffixarray /tmp/artist_${TYPE}_db.txt $PORT\" | gdb ../amuzi_search" &
    else
        echo prod
        ../amuzi_search suffixarray /tmp/artist_${TYPE}_db.txt $PORT &
    fi

    cd -

    PORT=`expr $PORT + 1`
done
