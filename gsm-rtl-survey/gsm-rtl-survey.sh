#!/bin/bash
# Variables
SDB="btsdb.db"
KRS="kal-rtl-scan.txt"
PPM=58
GAIN=40
KALBIN="/home/lenovo/tmp/gsm-rtl-survey/kalibrate-rtl/src/kal"
GSMRECPATH="/home/lenovo/tmp/gr-gsm/apps/"
TSHARKBIN="/usr/bin/tshark"

# Initialize database if it doesnt exist
if [ ! -f "$SDB" ]
then
    sqlite3 -init btsdb_sqlite3.sql $SDB ".quit"
fi
# Run kalibrate to scan the GSM band
$KALBIN -s GSM900 -g $GAIN -e $PPM > kal-rtl-scan.txt
# We extract ARFCN numbers from kalibrate output
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
    
    timeout 15s airprobe_rtlsdr.py -g $GAIN -p $PPM -f $freq &
    timeout 15s tshark -T pdml -i lo -Y '!icmp && gsmtap' > arfcn$arfcn.xml
done
} < $KRS

# now we have plenty of *.xml files, each corresponding to ARFCN freq.
# We process file by file with a python script.
arfcnxml=`ls *.xml`
for f in $arfcnxml
do
	echo "Processing $f"
    python pdml2sqlite.py $f
	#rm -rf $f
done
