#!/usr/bin/python
"""
This script will configure btsdb.
The configuration will be stored in a SQLite3 database btsdb.db.
"""
import sys
import sqlite3, getopt

cell_id=['GSM.Identity.MCC','GSM.Identity.MNC','GSM.Identity.LAC','GSM.Identity.CI','GSM.Identity.ShortName','GSM.Radio.C0','GSM.Radio.Band']

def show(conn, c, args):
    """
    Display all configuration keys from table: BTSDB
    """
    if(args[1] == "all"):
        """Display all config keys"""
        c.execute("""SELECT * FROM BTSDB""")
        fieldnames=[f[0] for f in c.description]
        print fieldnames
        for item in c.fetchall():
			print(item)
            #print("%s\t%s"%(item[0], item[1]))
            
    else:
        """Display config key"""
        c.execute("""SELECT * FROM CONFIG WHERE KEYSTRING='%s'""" %(args[1]))
        item = c.fetchone()
        print("%s=%s\nComment:%s" %(item[0], item[1], item[4]))

def set(conn, c, value, param):
		#param = ('GSM.Radio.Band', 'GSM.Radio.C0', 'timestamp', 'GSM.Radio.NeighbourARFCNs', 'GSM.Identity.MNC', 'GSM.Identity.BSIC.NCC', 'id', 'GSM.Identity.ShortName', 'GSM.Identity.BSIC.BCC', 'GSM.Identity.CI', 'GSM.Identity.MCC', 'GSM.Identity.LAC')
		#print ("""UPDATE BTSDB set VALUESTRING='%s' WHERE KEYSTRING='%s'"""%(value, param))
		c.execute("""INSERT INTO BTSDB {pn} VALUES {vn}""".format(pn=param,vn=value))
		print("Setting: %s=%s"%(param, value))
 
def help():
    print("\nDescription:")

    
#if __name__ == "__main__":
#    main()

