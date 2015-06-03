#!/bin/bash

SDB="btsdb.db"
KRS="kal-rtl-scan.txt"

arfcnxml=`ls *.xml`
for f in $arfcnxml
do
	echo "Processing $f"
    python pdml2sqlite.py $f
done
