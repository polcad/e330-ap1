from xml.etree import cElementTree as ET
import sys
import datetime
import re
import sqlite3, getopt
import btsdb

regex = {'current_strength': re.compile("RXLEV-FULL-SERVING-CELL:.*dBm \((\d+)\)"),
         'num_cells': re.compile("NO-NCELL-M:.*result \((\d+)\)"),
         'cell_report': re.compile("RXLEV-NCELL: (\d+)\n.*= BCCH-FREQ-NCELL: (\d+)\n.* = BSIC-NCELL: (\d)"),
         'arfcn': re.compile("GSM TAP Header, ARFCN: (\d+)"),
         'sys_info_2': re.compile("List of ARFCNs =([ \d]+).*(\d{4} \d{4}) = NCC Permitted",re.DOTALL),
         }
#bts_id = {'id' '0', 'timestamp', 'GSM.Identity.BSIC.BCC', 'GSM.Identity.BSIC.NCC', 'GSM.Identity.CI', 'GSM.Identity.LAC', 'GSM.Identity.MCC', 'GSM.Identity.MNC', 'GSM.Identity.ShortName', 'GSM.Radio.C0', 'GSM.Radio.Band', 'GSM.Radio.NeighbourARFCNs'}
bts_id = {'timestamp': '0000-00-00 00:00:00', 'GSM.Identity.BSIC.BCC': '0', 'GSM.Identity.BSIC.NCC': '0', 'GSM.Identity.CI': '0', 'GSM.Identity.LAC': '0', 'GSM.Identity.MCC': '000', 'GSM.Identity.MNC': '00', 'GSM.Identity.ShortName': 'xxx', 'GSM.Radio.C0': '0', 'GSM.Radio.Band': '000', 'GSM.Radio.NeighbourARFCNs': '0', 'Cell ARFCNs': '0', 'Cell IMSIs': '0', 'Cell TMSIs': '0'}

#e = ET.parse('29-dissections-pdml.xml').getroot()
e = ET.parse(sys.argv[1]).getroot()
#For description on System Information (SI) messages see:
#http://www.rfwireless-world.com/Terminology/GSM-system-information-messages.html

num_packets = 0
num_proto = 0

#for aptype in e.getiterator('proto'):
	#num_proto += 1
	#if aptype.attrib['showname'] == "GSM CCCH - System Information Type 2":
		#for atype in e.getiterator('field'):
			#if "List of ARFCNs =" in atype.attrib['show']:
				#print "ARFCNs= ",atype.attrib['show'].split("= ")[1]
				#break
#timestamp = datetime.datetime.now().strftime('%Y-%m-%d %H:%M:%S')
#bts_id['timestamp'] = timestamp
bts_id['Cell TMSIs'] = 0
bts_id['Cell IMSIs'] = 0
flag19 = 0
flag1a = 0

for atype in e.iter('field'):
    #print atype.tag,atype.attrib, "\n"
    if atype.attrib['name'] == "timestamp":
		bts_id['timestamp'] = atype.attrib['show']
    if atype.attrib['name'] == "e212.mcc":
		bts_id['GSM.Identity.MCC'] = atype.attrib['show']
    if atype.attrib['name'] == "e212.mnc":
		bts_id['GSM.Identity.MNC'] = atype.attrib['show']
                bts_id['GSM.Identity.ShortName'] = atype.attrib['showname'].split(": ")[1]
    if atype.attrib['name'] == "gsm_a.lac":
		bts_id['GSM.Identity.LAC'] = atype.attrib['show']
    if atype.attrib['name'] == "gsm_a.bssmap.cell_ci":
		bts_id['GSM.Identity.CI'] = atype.attrib['show']

    if atype.attrib['name'] == "gsm_a.dtap.msg_rr_type" and atype.attrib['value'] == "19": # Message Type: System Information Type 1
        flag19 = 1
    elif atype.attrib['name'] == "gsm_a.dtap.msg_rr_type" and atype.attrib['value'] == "1a": # Message Type: System Information Type 2
        flag1a = 1
    if "List of ARFCNs =" in atype.attrib['show'] and flag19:
	bts_id['Cell ARFCNs'] = atype.attrib['show'].split("= ")[1]
        flag19 = 0
    elif "List of ARFCNs =" in atype.attrib['show'] and flag1a:
	bts_id['GSM.Radio.NeighbourARFCNs'] = atype.attrib['show'].split("= ")[1]
        flag1a = 0

    if atype.attrib['name'] == "gsm_a.rr.ncc_permitted":
		bts_id['GSM.Identity.BSIC.NCC'] = atype.attrib['show']
#    if atype.attrib['name'] == "gsm_a.rr.elem_id":
#		for atype in e.getiterator('field'):
    if 'show' in atype.attrib:
		if "Single channel" in atype.attrib['show']:
				for s in atype.attrib['show'].split():
					if s.isdigit():
						bts_id['GSM.Radio.C0'] = int(s)

    if atype.attrib['name'] == "gsm_a.tmsi":
        bts_id['Cell TMSIs'] = bts_id['Cell TMSIs'] + 1
    if atype.attrib['name'] == "gsm_a.imsi":
        bts_id['Cell IMSIs'] = bts_id['Cell IMSIs'] + 1					

print bts_id
# Enter the informaton to database	
db_path = "btsdb.db"
conn = sqlite3.connect(db_path)
conn.text_factory = str
c = conn.cursor()   
print "Database: ", db_path
args=['all','all']
btsdb.set(conn, c, tuple(bts_id.values()), tuple(bts_id.keys()))
#c.execute("""UPDATE BTSDB set VALUESTRING='%s' WHERE KEYSTRING='GSM.Identity.MCC'"""%(value[0]))
btsdb.show(conn, c, args)

conn.commit()
conn.close()
