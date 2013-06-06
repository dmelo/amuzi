#!/bin/bash



rm /tmp/artist_album_db.txt
echo "select concat(lower(artist.name), 'A', lower(album.name)) from artist join album on artist.id = album.artist_id" | mysql -u root -pcafess123 youbetter > /tmp/artist_album_db.txt.tmp
tail -n +3 /tmp/artist_album_db.txt.tmp > /tmp/artist_album_db.txt

rm /tmp/artist_track_db.txt
echo "select concat(lower(artist.name), 'A', lower(music_title.name)) from artist join artist_music_title on artist.id = artist_music_title.artist_id join music_title on artist_music_title.music_title_id = music_title.id" | mysql -u root -pcafess123 youbetter > /tmp/artist_track_db.txt.tmp
tail -n +3 /tmp/artist_track_db.txt.tmp > /tmp/artist_track_db.txt

cd library/amuzi_search/env_album_db/
rm -rf data/*



if [[ $# -eq 1 && $1 = 'debug' ]]
then
    echo debug
    xterm -fg white -bg black -e 'echo "r suffixarray /tmp/artist_album_db.txt 3673" | gdb ../amuzi_search' &
else
    echo prod
    ../amuzi_search suffixarray /tmp/artist_album_db.txt 3673 &
fi

cd -


cd library/amuzi_search/env_track_db/
rm -rf data/*

if [[ $# -eq 1 && $1 = 'debug' ]]
then
    echo debug
    xterm -fg white -bg black -e 'echo "r suffixarray /tmp/artist_track_db.txt 3674" | gdb ../amuzi_search' &
else
    echo prod
    ../amuzi_search suffixarray /tmp/artist_track_db.txt 3674 &
fi
