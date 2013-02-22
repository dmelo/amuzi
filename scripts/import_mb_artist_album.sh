#!/bin/bash
psql -U musicbrainz -c "\copy (select concat(artist_name.id, 'A', lower(release_name.name)) from release_name join release on release_name.id = release.name join artist_credit on release.artist_credit = artist_credit.id join artist_name on artist_credit.name = artist_name.id group by artist_name.id, release_name.name) To '../public/data/artist_album.txt' with CSV" musicbrainz_db

sed -i 's/^\"\(.*\)\"$/\1/g' ../public/data/artist_album.txt
