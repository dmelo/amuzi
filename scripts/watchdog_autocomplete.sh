#!/bin/bash

curl --insecure -m 5 http://amuzi.me/autocomplete.php?q=coldplay >> /tmp/watchdog_autocomplete.log

if [ $? -ne 0 ]
then
    echo 'first try failed. trying again...'
    curl --insecure -m 5 http://amuzi.me/autocomplete.php?q=coldplay >> /tmp/watchdog_autocomplete.log

    if [ $? -ne 0 ]
    then
        echo 'second try failed. restarting autocomplete...'
        ./scripts/start_amuzi_search.sh
        ./scripts/start_amuzi_search_db.sh
    else
        echo 'second try went ok'
    fi
else
    echo 'first try went ok'
fi
