#!/bin/bash

DATAPATH=public/api/tmp/cache
THRESHOLD=`expr 15 \* 1024 \* 1024`

while [ $THRESHOLD -le `du $DATAPATH | cut -f 1` ]
do
    LIST=`ls -t $DATAPATH/*.access`
    if [ $? -eq 0 ]
    then
        FILE=`echo $LIST | tail -n 1 | sed 's/.access//g'`
        echo $FILE
        yes | rm -- $FILE $FILE.*
    else
        echo ".access files doesn't exists and total data is above $THRESHOLD"
        exit 2
    fi
done

