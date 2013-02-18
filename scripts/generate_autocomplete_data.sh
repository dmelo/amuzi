#!/bin/bash


psql -U musicbrainz -c "\copy (select artist_name.name from artist_name) To '/tmp/artist.csv' with CSV" musicbrainz_db


psql -U musicbrainz -c "\copy (select track_name.name from track_name) To '/tmp/music_title.csv' with CSV" musicbrainz_db


psql -U musicbrainz -c "\copy (select release_name.name from release_name) To '/tmp/album.csv' with CSV" musicbrainz_db
