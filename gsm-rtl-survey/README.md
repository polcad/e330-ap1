# Description

This folder contains a set of bash and Python scripts for local site GSM survey.
An RTL2832U based USB DVB-T tuner is used to scan GSM900 frequency band and the results will be stored in a local Sqlite3 database. The database will be crated automatically is it doesn't exist.

# Status

05.2015 Fully working and tested. It is however still work in progress. The code is ugly.

# Install

You will need Wireshark, UHD, Gnuradio and gr-gsm (updated version of Airprobe. Check here: https://github.com/ptrkrysik/gr-gsm)
Create a folder gsm-rtl-survey and copy all files to the folder.
You will also have to clone a git repository of kalibrate-rtl. If you don't have a live internet connection you can unzip packed repository kalibrate-rtl.tar.gz that you will find among the files.

    mkdir gsm-rtl-survey
    cd gsm-rtl-survey
    git clone https://github.com/steve-m/kalibrate-rtl.git
    cd kalibrate-rtl
    ./bootstrap
    ./configure
    make
    cd ..

The script do not require you to "make install" kalibrate. To initialize the database execute

    sqlite3 -init btsdb_sqlite3.sql $SDB ".quit"

# Run

First you need to make sure you calibrated your RTL dongle. They have a large frequency error and can give false ARFCN readings. In order to calibrate your dongle run:

    kal -s GSM900 -g 50

It should return a list of ARFCN channels that it finds in your area. Pick one of the channels and run:

    kal -s GSM900 -a 25 -g 50

This will use ARFCN=25 to kalibrate the dongle. It will also print average ppm error in the results . Run kalibrate again specifying this ppm in the options, ie. if the ppm was 55:

    kal -s GSM900 -g 50 -e 55

kalibrate will print a list of ARFCN it found and frequency error.
You can run kalibrate several times changing the value in "-e" option untill you get 0Hz error. This will probably not be possible, as RTL dongles are poor quality and this frequency error is usually large and varying with time and frequency.
Next, update the script "gsm-rtl-survey.sh" with your PPM value.

Now the system is calibrated and ready to run. You run the survey with command:

    gsm-rtl-survey.sh

You may want to update the script with a longer capture time. 10s seconds is usually enough.

    timeout 5s airprobe_rtlsdr.py ...
    timeout 5s tshark ...

"gsm-rtl-survey.sh" script will produce "kal-rtl-scan.txt" file containing the list of local ARFCNs in use and a number of *.xml files. Each *.xml file corresponds to ARFCN and contains a capture of the GSM signal at the particular ARFCN decoded by tshark (Wireshark). The script "pdml2sqlite.py" will then extract the data from all *.xml capture files and put it in an Sqlite3 database.

## BTS parameters being captured:
'timestamp', 'GSM.Identity.BSIC.BCC', 'GSM.Identity.BSIC.NCC', 'GSM.Identity.CI',
'GSM.Identity.LAC', 'GSM.Identity.MCC', 'GSM.Identity.MNC', 'GSM.Identity.ShortName',
'GSM.Radio.C0', 'GSM.Radio.Band', 'GSM.Radio.NeighbourARFCNs'
