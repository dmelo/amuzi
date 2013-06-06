#!/bin/bash

PID=`ps aux | grep amuzi_search | grep 3673 | grep -v grep | sed 's/  */ /g' | cut -d\  -f 2`
kill -9 $PID
PID=`ps aux | grep amuzi_search | grep 3674 | grep -v grep | sed 's/  */ /g' | cut -d\  -f 2`
kill -9 $PID

killall -9 gdb
killall -9 amuzi_search
killall -9 xterm
