#!/bin/bash
SDB="btsdb.db"

if [ ! -f $SDB ]
then
    sqlite3 -init btsdb_sqlite3.sql $SDB ".quit"
fi

