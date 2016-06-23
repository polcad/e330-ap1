#!/bin/bash
#2015-01-03 rev0.2
#Version 0.20


#get sudo privileges
sudo echo 
python $HOME/bin/e330-ap1/www-data/events/proxyman/ProxyMan.py

#store FreeSWITCH and OpenBTS root locations
OBTS_ROOT="$HOME/openbts"
FS_ROOT="/usr/local/freeswitch"

sudo killall transceiver &> /tmp/tmp
#ulimit -s 240
sleep 1s
#initialize hardware
uhd_usrp_probe

#export mytitle=FreeSwitch
#sudo gnome-terminal -x sh -c "sudo ./freeswitch"&
export mytitle=AsteriskCLI
sudo gnome-terminal --title "ASTERISK_CLI" -x sh -c "sudo asterisk -vvvvvr"&


#start smqueue and sipauthserve
cd $OBTS_ROOT/public
export mytitle=sipauthserve
sudo gnome-terminal --title "SMQUEUE_SIPAUTHSERVE" --tab --title "SMQUEUE" -e "sudo smqueue/trunk/smqueue/smqueue" --tab --title "SIPAUTHSERVE" -e "sudo subscriberRegistry/trunk/sipauthserve" &

#start OpenBTS
cd $OBTS_ROOT/public/openbts/trunk/apps
sudo killall transceiver &> /tmp/tmp #sometimes necessary
export mytitle=OpenBTS
sudo gnome-terminal --title "OPENBTS_CLI" -x sh -c  "sudo ./OpenBTSCLI" &

sudo gnome-terminal --title "OPENBTS" -x sh -c "while true; do sudo ./OpenBTS && break; done" &

#linphone &
