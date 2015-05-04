#!/bin/bash

chmod -R 777 public/api/cache/
cp vendor/dmelo/you2better/conf.php.template vendor/dmelo/you2better/conf.php
./vendor/rg3/youtube-dl/youtube-dl -U
