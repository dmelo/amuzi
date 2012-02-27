#!/bin/bash

FILE=/tmp/log-amuzi-`date +'%N'`.txt

SCRIPTS="countLines.sh phpcpd.sh phpcs.sh phplint.sh phpunit.sh"
for S in $SCRIPTS
do
    echo $S
    echo "BEGIN ${S} ===================================" >> $FILE
    ./$S >> $FILE
    echo "END ${S} ===================================" >> $FILE
    echo >> $FILE
    echo >> $FILE
done

echo "Done. log file is ${FILE}"
