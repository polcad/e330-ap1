#!/bin/bash

######################################################################
#       FakeBTS.com
#       2014
#       v 0.1.6
#		updated by Dictador, June 2015
#######################################################################
#
#   Copyright (C) 2014 Pedro Cabrera
#
#    This program is free software: you can redistribute it and/or modify
#    it under the terms of the GNU General Public License as published by
#    the Free Software Foundation, either version 3 of the License, or
#    (at your option) any later version.
#
#    This program is distributed in the hope that it will be useful,
#    but WITHOUT ANY WARRANTY; without even the implied warranty of
#    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#    GNU General Public License for more details.
#
#    You should have received a copy of the GNU General Public License
#    along with this program.  If not, see <http://www.gnu.org/licenses/>.
#
#    Contact Info:
#    @PCabreraCamara
#    pedrocab@gmail.com
#
#######################################################################

# Variables
KALBIN="/home/lenovo/tmp/gsm-rtl-survey/kalibrate-rtl/src/kal"
#GSMRECPATH="/opt/airprobe/gsm-receiver/src/python/"
GSMRECPATH="/home/lenovo/tmp/gr-gsm/apps/"
TSHARKBIN="/usr/bin/tshark"
cellscan="cellscan.csv"
# GSM900:	GSM 900Mhz
# DSC:		GSM 1800Mhz
#BANDAS="GSM900 DCS"
BANDAS=GSM900
PPM=58
GAIN=40
# RUTA de FICHEROS DE SALIDA
SCRIPTDIR="$( cd "$( /usr/bin/dirname "$0" )" && pwd )"
# X11 Display (airprobe)
DISPLAY=:0.0
export DISPLAY

# 0x19, "System Information Type 1"
# 0x1a, "System Information Type 2"
# 0x1b, "System Information Type 3"
# 0x21, "Paging Request Type 1" 

# Licencia
function disclaimer {
        echo "CellAnalysis  Copyright (C) 2014 Pedro Cabrera"
        echo "This program comes with ABSOLUTELY NO WARRANTY; for details visit http://www.gnu.org/licenses/gpl.txt"
        echo "This is free software, and you are welcome to redistribute it"
        echo "under certain conditions; for details visit http://www.gnu.org/licenses/gpl.txt."
        echo
}

disclaimer

# Checking for required components
if [ ! -f "${KALBIN}" ]
then
	echo "Exit !!, ${KALBIN} does not exist. Check the path or install the program."
	exit 0
fi

#if [ ! -f "${GSMRECPATH}gsm_receive_rtl.py" ]
if [ ! -f "${GSMRECPATH}airprobe_rtlsdr.py" ]
then
        echo "Exit !!, ${GSMRECPATH}gsm_receive_rtl.py does not exist. Check the path or install the program."
        exit 0
fi

if [ ! -f "${TSHARKBIN}" ]
then
        echo "Exit !!, ${TSHARKBIN} does not exist. Check the path or install the program."
        exit 0
fi

echo "Looking for GSM stations in range..."
echo > /tmp/out_kal.txt
for band in ${BANDAS}
do
	${KALBIN} -s ${band} -g $GAIN -e $PPM &>> /tmp/out_kal.txt
done

grep "chan:" /tmp/out_kal.txt > /dev/null 2>&1

if [ $? -eq 0 ]
then

	grep "chan:" /tmp/out_kal.txt | while read linea
	do
		arfcn=`echo "${linea}" | awk '{print $2}'`
		freq_tmp=`echo "${linea}" | awk '{print $3}' | sed 's/(//g'| sed 's/MHz//g' | sed 's/\.//g'`
		freq_base=`echo ${freq_tmp}00000`
		signo=`echo "${linea}" | awk '{print $4}'`
		offset=`echo "${linea}" | awk '{print $5}' | sed 's/power://g'| sed 's/)//g'| sed 's/kHz//g' | sed 's/Hz//g' |sed 's/\.//g'`
		freq_final=`echo $[${freq_base} ${signo} ${offset}]`
		#freq_final=`echo $[${freq_base}]`
		num_chan=0

		"${TSHARKBIN}" -i lo -a duration:10 -w /tmp/tshark_${arfcn}.pcap > /dev/null 2>&1 &
		echo "Processing ARFCN nr: ${arfcn}"
		echo "freq_tmp = ${freq_tmp}"
		echo "offset =  ${signo}${offset}"
		echo "freq_final = ${freq_final}"
		cd ${GSMRECPATH} > /dev/null 2>&1
		#./gsm_receive_rtl.py -s 1e6 -f ${freq_final} -g 42 > /dev/null 2>&1 &
		./airprobe_rtlsdr.py -s 2e6 -g $GAIN -p $PPM -f ${freq_final}  > /dev/null 2>&1 &
        	sleep 20
		disown
		kill -9 $! > /dev/null
		cd - > /dev/null 2>&1

		# Number of subscribers
                "${TSHARKBIN}" -r /tmp/tshark_${arfcn}.pcap -Y '!icmp && gsmtap' -T pdml -2 -R "gsm_a.dtap.msg_rr_type == 0x21" > /tmp/tshark_subs_${arfcn}.txt 2>&1
		grep "MSI" /tmp/tshark_subs_${arfcn}.txt > /dev/null 2>&1
		if [ $? -eq 0 ]
		then
			num_imsis=`grep "gsm_a.imsi" /tmp/tshark_subs_${arfcn}.txt| sort -u | wc -l`
			num_tmsis=`grep "gsm_a.tmsi" /tmp/tshark_subs_${arfcn}.txt| sort -u | wc -l`
			num_subs=num_imsis+num_tmsis
			num_subs=`echo $[${num_imsis} + ${num_tmsis}]`
		else
			num_subs=0
		fi

		# Number of CCCH frames captured
		"${TSHARKBIN}" -z io,phs -r /tmp/tshark_${arfcn}.pcap > /tmp/num_frames_${arfcn}.txt 2>&1
		grep ccch /tmp/num_frames_${arfcn}.txt > /dev/null 2>&1
		if [ $? -eq 0 ]
		then
			num_lin=`grep ccch /tmp/num_frames_${arfcn}.txt | awk -F"frames:" '{print $2}'| cut -d" " -f1`
		else
			num_lin=0
		fi
		rm /tmp/num_frames_${arfcn}.txt

                umbral=6
                umbral_subs=`echo $[$num_lin * 0,2]`

		# Number of ARFCN channels and their numbers in the cell
                "${TSHARKBIN}" -r /tmp/tshark_${arfcn}.pcap -c 1 -T pdml -2 -R "gsm_a.dtap.msg_rr_type == 0x19" > /tmp/tshark_canales_${arfcn}.txt 2>&1
		grep "List" /tmp/tshark_canales_${arfcn}.txt > /dev/null 2>&1
		if [ $? -eq 0 ]
		then
	                num_chan=`cat /tmp/tshark_canales_${arfcn}.txt |grep "List"| awk -F"\"" '{print $4}'| cut -d= -f2| awk '{print NF}'`
			chan_list=`cat /tmp/tshark_canales_${arfcn}.txt |grep "List"| awk -F"\"" '{print $4}'| cut -d= -f2`
       		        if [ ${num_chan} -eq 1 ]
			then
                       		channel=`cat /tmp/tshark_canales_${arfcn}.txt | grep "List"| awk -F"\"" '{print $4}'| cut -d= -f2`
                	fi
		else
			num_chan=0
			channel=666
		fi

		# Neighbour ARFCN channels
                "${TSHARKBIN}" -r /tmp/tshark_${arfcn}.pcap -c 1 -T pdml -2 -R "gsm_a.dtap.msg_rr_type == 0x1a" > /tmp/tshark_neighbours_${arfcn}.txt 2>&1
		grep "List" /tmp/tshark_neighbours_${arfcn}.txt > /dev/null 2>&1
		if [ $? -eq 0 ]
		then
	                num_neighbours=`cat /tmp/tshark_neighbours_${arfcn}.txt |grep "List of ARFCNs"| awk -F"\"" '{print $4}'| cut -d= -f2| awk '{print NF}'`
			neighbours_list=`cat /tmp/tshark_neighbours_${arfcn}.txt |grep "List of ARFCNs"| awk -F"\"" '{print $4}'| cut -d= -f2`
       		        if [ ${num_neighbours} -eq 1 ]
			then
                       		channel=`cat /tmp/tshark_neighbours_${arfcn}.txt | grep "List of ARFCNs"| awk -F"\"" '{print $4}'| cut -d= -f2`
                	fi
		else
			num_neighbours=0
			nchannel=666
		fi

               	# Looking for CellID and LAC
                "${TSHARKBIN}" -r /tmp/tshark_${arfcn}.pcap -c 1 -T pdml -2 -R "gsm_a.dtap.msg_rr_type == 0x1b" > /tmp/tshark_cellid_${arfcn}.txt 2>&1
		grep "Cell CI" /tmp/tshark_cellid_${arfcn}.txt > /dev/null 2>&1
		if [ $? -eq 0 ]
		then
                	#cellid=`cat /tmp/tshark_cellid_${arfcn}.txt |grep "Cell CI" | awk -F"show=" '{print $2}'| cut -d"\"" -f2|awk '{print $3}'| sed 's/0x//'`
			cellid=`cat /tmp/tshark_cellid_${arfcn}.txt |grep "Cell CI"| awk -F"show=" '{print $2}'| cut -d"\"" -f2|cut -d"(" -f3| sed 's/)//g'`
                	lac=`cat /tmp/tshark_cellid_${arfcn}.txt |grep "LAC"| awk -F"show=" '{print $2}'| cut -d"\"" -f2|cut -d"(" -f3| sed 's/)//g'`
			mcc=`cat /tmp/tshark_cellid_${arfcn}.txt |grep "MCC"| awk -F"show=" '{print $2}'| cut -d"\"" -f2|cut -d"(" -f3| sed 's/)//g'`
			mnc=`cat /tmp/tshark_cellid_${arfcn}.txt |grep "MNC"| awk -F"show=" '{print $2}'| cut -d"\"" -f2|cut -d"(" -f3| sed 's/)//g'`
			operador=`cat /tmp/tshark_cellid_${arfcn}.txt |grep "MNC"| awk -F"showname=" '{print $2}'| cut -d"\"" -f2|cut -d":" -f2 | cut -d"(" -f1`
		else
			cellid=0
			lac=0
			mcc=0
			mnc=0
			operador=""
		fi
	
		#rm /tmp/tshark_subs_${arfcn}.txt	
                #rm /tmp/tshark_canales_${arfcn}.txt
		#rm /tmp/tshark_cellid_${arfcn}.txt

		if [ ${num_lin} -ne 0 ]
                then
                        if [ ${num_chan} -eq 1 ] && [ ${num_subs} -lt ${umbral_subs} ]
                        then
                                hora=`date +"%d/%m %H:%M"`
				echo "Cell with only one ARFCN!!, Alarm and LAC: ${lac}, CellID: ${cellid}, arfcn: ${arfcn}"
                                echo "${hora};${lac}-${cellid};${arfcn};${mcc};${mnc};${operador};${num_imsis};${num_tmsis};${num_subs};${num_chan};${chan_list};${num_neighbours};${neighbours_list}" >> ${SCRIPTDIR}/alarms.csv
			elif [ ${num_chan} -gt 1 ] && [ ${num_subs} -lt ${umbral_subs} ]
                        then
                                hora=`date +"%d/%m %H:%M"`
                                echo "Posible alarm in LAC: ${lac}, CellID: ${cellid}, arfcn: ${arfcn}"
                                echo "${hora};${lac}-${cellid};${arfcn};${mcc};${mnc};${operador};${num_imsis};${num_tmsis};${num_subs};${num_chan};${chan_list};${num_neighbours};${neighbours_list}" >> ${SCRIPTDIR}/alarms.csv
                        else
                                hora=`date +"%d/%m %H:%M"`
				echo "Traffic data are written to the file: ${cellscan}"
                            if [ ! -f "${cellscan}" ]
                                then
                                echo "time;lac-cellid;arfcn;mcc;mnc;operator;num_imsis;num_tmsis;num_subs;num_chan;chan_list;num_neighbours;neighbours_list" >> ${SCRIPTDIR}/${cellscan}
                            else
                                echo "${hora};${lac}-${cellid};${arfcn};${mcc};${mnc};${operador};${num_imsis};${num_tmsis};${num_subs};${num_chan};${chan_list};${num_neighbours};${neighbours_list}" >> ${SCRIPTDIR}/${cellscan}
                            fi
                          
                        fi
                else
                        hora=`date +"%d/%m %H:%M"`
			echo "Measurements for channel ${arfcn} are ignored."
                        echo "${hora};${lac}-${cellid};${arfcn};${mcc};${mnc};${operador};${num_imsis};${num_tmsis};${num_subs};${num_chan};${chan_list};${num_burst};${num_drop};${num_fbsb}" >> ${SCRIPTDIR}/ignore.csv
                fi
	
	done	

	echo "Finished"
else
        mytime=`date +"%d/%m %H:%M"`
        echo "Error, No GSM stations found"
        echo "${mytime} No GSM stations found" >> ${SCRIPTDIR}/error.log
fi 
