#!/bin/bash

function parseini() {
    php scripts/parseini.php application/configs/application.ini production resources.db.params.$1 2> /dev/null
}


PID=`ps aux | grep amuzi_search | grep -e 3673 -e 3672 -e 3671 -e 3682 -e 3683 | grep -v grep | sed 's/  */ /g' | cut -d\  -f 2`
if ! [ $PID -n ]
then
    kill -9 $PID
fi



DB_USER=`parseini username`
DB_PASS=`parseini password`
DB_NAME=`parseini dbname`
DB_HOST=`parseini host`

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
            echo $SELECT
            FILE="/tmp/artist_${TYPE}_db${SUFFIX}"
            echo "$SELECT" | mysql -u $DB_USER -h $DB_HOST -p$DB_PASS $DB_NAME > ${FILE}.tmp
            tail -n +3 /tmp/artist_${TYPE}_db.txt.tmp > ${FILE}
            rm ${FILE}.tmp
            DATA_PATH="library/amuzi_search/env_${TYPE}_db${SUFFIX}-d"
            mkdir -p $DATA_PATH/data
            cd $DATA_PATH

            rm -rf data/*


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
