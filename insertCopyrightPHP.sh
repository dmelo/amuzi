#!/bin/bash

FILES="`find application | grep '\.php$'` `find scripts | grep '\.php$'` `find tests/application | grep '\.php$'` tests/bootstrap.php"

for file in $FILES
do
    grep "* This program is free software:" $file
    if [ 1 -eq $? ]
    then
        echo "handling $file"
        head -n 2 $file > $file.tmp
        cat copyright.aux >> $file.tmp
        TOTAL=`wc -l $file | cut --delimiter=' ' -f 1`
        TOTAL=`expr $TOTAL - 2`
        tail -n $TOTAL $file >> $file.tmp
        mv $file.tmp $file
    fi

    grep "* IndexController" $file
    if [ 0 -eq $? ]
    then
        CLASS=`grep "^class" $file | cut --delimiter=' ' -f 2`
        sed -i "s/\* IndexController/* $CLASS/g" $file
    fi
done
