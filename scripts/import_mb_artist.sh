#!/bin/bash

psql -U musicbrainz -c "\copy (select concat(artist_name.id, 'AA', lower(artist_name.name)) from artist_name) To '/var/tmp/artist.csv' with CSV" musicbrainz_db

