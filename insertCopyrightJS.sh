#!/bin/bash

FILES="bootstrap-slide.js bootstrap-tutorial.js commands.js default.js facebook-connect.js incboard* jquery.bootstrap* jquery.subtitle.js resultset.js share.js"

for file in $FILES
do
    file=public/js/${file}
    grep "* This program is free software:" $file
    if [ 1 -eq $? ]
    then
        cat copyright.aux $file >> $file.tmp
        mv $file.tmp $file
    fi

    grep "* IndexController" $file
    if [ 0 -eq $? ]
    then
        CLASS=`echo $file | awk -F/ '{print $NF}'`
        sed -i "s/\* IndexController/* $CLASS/g" $file
    fi
done
