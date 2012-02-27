#!/bin/bash

for file in  `find application/ library/DZend/ library/LightningPackerHelper/ | grep "\.php$"`
do
    php -l $file
done
