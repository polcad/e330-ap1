#!/bin/bash
#2015-01-03 rev0.2
# This script will call text editor with path to all important config and log files
# Asterisk:
#  extensions.conf
#  sip.conf
#  dongle.conf
# OpenBTS log files:
#  ussd.txt
#  sms.txt
sudo echo
sudo gedit /etc/asterisk/extensions.conf /etc/asterisk/sip.conf /etc/asterisk/dongle.conf &
sudo gedit /var/log/asterisk/ussd.txt /var/log/asterisk/sms.txt &



