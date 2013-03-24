#!/bin/bash

DATAPATH=public/api/tmp/cache/
THRESHOLD=`expr 14 \* 1024 \* 1024`

while [ $THRESHOLD -le `du $DATAPATH | cut -f 1` ]
do
    FILE=`ls -t $DATAPATH/*.access | tail -n 1`
    echo $FILE
    rm -- `echo $FILE | sed 's/\.access//g'`
    rm -- $FILE
done

