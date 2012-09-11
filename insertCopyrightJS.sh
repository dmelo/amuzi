#!/bin/bash

for file in $@
do
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
