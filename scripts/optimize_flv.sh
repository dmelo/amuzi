#!/bin/bash

PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin
DATAPATH=public/api/tmp/cache

LIST=`ls -l public/api/tmp/cache/  | grep "\.flv$" | grep -v "internal" | sed 's/  */ /g' | cut -d\  -f 5,9 | sort -rn | cut -d\  -f 2`

for FILE in $LIST
do
    FILE=$DATAPATH/$FILE
    ls $FILE.optimized
    if [ $? -ne 0 ]
    then
        echo $FILE
        ffmpeg -i $FILE -b 512k -y -s 480x270 $FILE-tmp.flv
	ls -lha -- $FILE $FILE-tmp.flv
        mv -f $FILE-tmp.flv $FILE
        touch $FILE.optimized
        exit 0
    fi
done
