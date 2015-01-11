#!/bin/bash
#2015-01-03 rev0.2
#get sudo privileges
sudo echo
#store FreeSWITCH and OpenBTS root locations
OBTS_ROOT="$HOME/openbts"
FS_ROOT="/usr/local/freeswitch"
sudo killall transceiver &> /tmp/tmp
#ulimit -s 240
sleep 1s
#start OpenBTS
cd $OBTS_ROOT/public/openbts/trunk/apps
sudo gnome-terminal --title "OPENBTS" -x sh -c "sudo ./OpenBTS" &

