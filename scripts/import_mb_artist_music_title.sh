#!/bin/bash

psql -U musicbrainz -c "\copy (select artist_name.name, track_name.name from track_name join track on track_name.id = track.name join artist_credit on track.artist_credit = artist_credit.id join artist_name on artist_credit.name = artist_name.id group by artist_name.name, track_name.name) To '/tmp/artist_track.csv' with CSV" musicbrainz_db

php import_mb_artist_music_title.php
