#!/bin/bash

echo "rebuilding mysql for phase 1a"
echo -e "drop database youbetter; create database youbetter; use youbetter; source scripts/experiment_incboard_1.init.sql;" | mysql -u youbetter -pyoubetter youbetter
cd scripts

echo "running experiment 1a";
php experiment_incboard_1a.php
cd -



