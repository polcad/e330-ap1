#!/bin/bash

SDB="btsdb.db"
KRS="kal-rtl-scan.txt"
PPM=55

if [ ! -f "$SDB" ]
then
    sqlite3 -init btsdb_sqlite3.sql $SDB ".quit"
fi

kalibrate-rtl/src/kal -s GSM900 -g 50 -e $PPM > kal-rtl-scan.txt

linesToSkip=1
{
    for ((i=$linesToSkip;i--;)) ;do
        read
        done
while read line
do
    name=$line
    freq="$(echo $line | cut -d'(' -f2 | cut -d'H' -f1)"
    arfcn="$(echo $line | cut -d' ' -f2)"
    echo "${freq}"
    echo "${arfcn}"
    
    timeout 5s airprobe_rtlsdr.py -g 50 -p $PPM -f $freq &
    timeout 5s tshark -T pdml -i lo -Y '!icmp && gsmtap' > arfcn$arfcn.xml
done
} < $KRS

arfcnxml=`ls *.xml`
for f in $arfcnxml
do
	echo "Processing $f"
    python pdml2sqlite.py $f
done
