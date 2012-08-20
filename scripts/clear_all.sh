#!/bin/bash

echo -e "drop database youbetter; create database youbetter; use youbetter; source setup/db/create.sql; source setup/db/populate.sql;" | mysql -u youbetter -pyoubetter youbetter
php scripts/clear_cache.php

