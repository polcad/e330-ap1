#!/bin/bash
# 2015-01-10
# Dictador
#
sudo echo Need sudo privilages
CWD = `pwd`
ASTCONFDIR = /etc/asterisk
CONFLIST = `ls *.conf`
for file in $CONFLIST
	do
		if [ -f $ASTCONFDIR/$file ]
		then
			sudo mv $ASTCONFDIR/$file $ASTCONFDIR/$file.bak
			sudo cp $CWD/$file $ASTCONFDIR/$file
		fi
	done
