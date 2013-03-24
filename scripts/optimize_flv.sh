#!/bin/bash

DATAPATH=public/api/tmp/cache

LIST=`ls -l public/api/tmp/cache/  | grep "\.flv$" | grep -v "internal" | sort -rn +4 | head -n 100 | sed 's/  */ /g' | cut -d\  -f 9`

for FILE in $LIST
do
    FILE=$DATAPATH/$FILE
    ls $FILE.optimized
    if [ $? -ne 0 ]
    then
        echo $FILE
        ffmpeg -i $FILE -b 512k -y -s 480x270 $FILE-tmp.flv
        mv -f $FILE-tmp.flv $FILE
        touch $FILE.optimized
        exit 0
    fi
done
