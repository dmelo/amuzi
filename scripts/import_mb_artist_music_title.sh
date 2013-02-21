#!/bin/bash

psql -U musicbrainz -c "\copy (select concat(artist_name.id, 'AA', lower(track_name.name)) from track_name join track on track_name.id = track.name join artist_credit on track.artist_credit = artist_credit.id join artist_name on artist_credit.name = artist_name.id where track_name.name not like '%(%)%' group by artist_name.id, lower(track_name.name)) To '/var/tmp/artist_track.csv' with CSV" musicbrainz_db

# php import_mb_artist_music_title.php
