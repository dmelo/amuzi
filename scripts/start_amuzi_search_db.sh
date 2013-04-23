#!/bin/bash

PID=`ps aux | grep amuzi_search | grep 3673 | grep -v grep | sed 's/  */ /g' | cut -d\  -f 2`
kill -9 $PID
PID=`ps aux | grep amuzi_search | grep 3674 | grep -v grep | sed 's/  */ /g' | cut -d\  -f 2`
kill -9 $PID


rm /tmp/artist_album_db.tmp
echo "select concat(lower(artist.name), 'A', lower(album.name)) from artist join album on artist.id = album.artist_id into outfile '/tmp/artist_album_db.txt' order by artist.name, album.name" | mysql -u youbetter -pyoubetter youbetter
cd library/amuzi_search/env_album_db/
rm -rf data/*
../amuzi_search suffixarray /tmp/artist_album_db.txt 3673 &

rm /tmp/artist_track_db.tmp
echo "select concat(lower(artist.name), 'A', lower(music_title.name)) from artist join artist_music_title on artist.id = artist_music_title.artist_id join music_title on artist_music_title.music_title_id = music_title.id into outfile '/tmp/artist_track_db.txt'" | mysql -u youbetter -pyoubetter youbetter
cd library/amuzi_search/env_track_db/
rm -rf data/*
../amuzi_search suffixarray /tmp/artist_track_db.txt 3674 &
