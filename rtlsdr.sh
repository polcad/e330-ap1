#!/bin/bash
# 10.05.2012
# polcad
sudo echo
if [ ! -e /etc/modprobe.d/rtlsdr.conf];
  sudo cp rtlsdr.conf /etc/modprobe.d/
elif [ -e /etc/modprobe.d/rtlsdr.conf];
  sudo rm -rf /etc/modprobe.d/rtlsdr.conf
fi
