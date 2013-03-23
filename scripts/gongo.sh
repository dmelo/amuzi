#!/bin/bash

function cpu_total() {
    cat /proc/stat  | grep "^cpu " | sed 's/^cpu *//g' | cut -d\  -f 1,2,3,4,5,6,7 | sed 's/ / + /g' | bc
}

function cpu_idle() {
    cat /proc/stat | grep "^cpu " | sed 's/^cpu *//g' | cut -d\  -f 4
}

if [ $# -eq 2 ]
then
    TOTAL=`cpu_total`
    IDLE=`cpu_idle`

    sleep 1

    TOTAL2=`cpu_total`
    IDLE2=`cpu_idle`

    IDLENESS=`echo "scale=0;($IDLE2 - $IDLE) * 100 / ($TOTAL2 - $TOTAL)" | bc`

    LOADAVG=$(echo "scale=0;`cat /proc/loadavg | cut -d\  -f 1` * 100" | bc | sed 's/\..*//g')

    if [ $IDLENESS -gt $1 -a $LOADAVG -lt $2 ]
    then
        echo GO
        exit 0
    else
        echo "Don't GO"
        exit 1
    fi
else
    echo "usage: gongo.sh min_cpu_idle max_load_average"
    echo
    echo "    min_cpu_idle - Minimum percentage of CPU that must be in idle. If 50 is specified, then half of the CPU must be idle."
    echo "    max_load_average - Upper limit for last minut load average. If 30 is specified, then last minut load average must be bellow 3.00"
    echo
    echo "    Only if both requisites matches the command will echo GO and return 0. Otherwise, it will echo \"Don't GO\" and return 1"
fi
