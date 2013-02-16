#!/bin/bash
psql -U musicbrainz -c "\copy (select artist_name.name, release_name.name from release_name join release on release_name.id = release.name join artist_credit on release.artist_credit = artist_credit.id join artist_name on artist_credit.name = artist_name.id group by artist_name.name, release_name.name) To '/tmp/artist_album.csv' with CSV" musicbrainz_db

php import_mb_artist_album.php
