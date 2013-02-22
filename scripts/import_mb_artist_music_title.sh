#!/bin/bash

psql -U musicbrainz -c "\copy (select concat(artist_name.id, 'A', lower(track_name.name)) from track_name join track on track_name.id = track.name join artist_credit on track.artist_credit = artist_credit.id join artist_name on artist_credit.name = artist_name.id where track_name.name not like '%(%)%' group by artist_name.id, lower(track_name.name)) To '../public/data/artist_track.txt' with CSV" musicbrainz_db

sed 's/^\"\(.*\)\"$/\1/g' ../public/data/artist_track.txt | sort -u > ../public/data/artist_track.txt.tmp
mv ../public/data/artist_track.txt.tmp ../public/data/artist_track.txt
