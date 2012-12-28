#!/bin/bash

DATAPATH=public/api/tmp/cache/
THRESHOLD=`expr 15 \* 1024 \* 1024`

while [ $THRESHOLD -le `du $DATAPATH | cut -f 1` ]
do
    FILE=`ls -t $DATAPATH/*.access | tail -n 1`
    echo $FILE
    echo "rm -- `echo $FILE | sed 's/\.access//g'`"
    echo "rm -- $FILE"
    break
done

