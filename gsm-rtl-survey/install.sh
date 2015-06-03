#!/bin/bash

DIRKAL="kalibrate-rtl"
SDB="btsdb.db"

if [ ! -d "$DIRKAL" ]
then
  git clone https://github.com/steve-m/kalibrate-rtl.git
  cd kalibrate-rtl
  ./bootstrap
  ./configure
  make
  cd ..
fi
#initialize databes to store BTS data
if [ ! -f $SDB ]
then
     sqlite3 -init btsdb_sqlite3.sql $SDB ".quit"
fi
