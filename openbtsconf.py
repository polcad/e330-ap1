#!/usr/bin/python
"""
This script will configure OpenBTS with MCC and MNC of your choice.
The configuration will be stored in a SQLite3 database.
SQlite3 /etc/OpenBTS/OpenBTS.db.
"""
import sys
import sqlite3, getopt

cell_id=['GSM.Identity.MCC','GSM.Identity.MNC','GSM.Identity.LAC','GSM.Identity.CI','GSM.Identity.ShortName','GSM.Radio.C0']
 
def show(conn, c, args):
    """
    Display all configuration keys from table: CONFIG
    """
    if(args[1] == "all"):
        """Display all config keys"""
        c.execute("""SELECT * FROM CONFIG""")
        for item in c.fetchall():
            print("%s\t%s"
                  %(item[0], item[1]))       
    elif(args[1] == "cellid"):
		"""Display Cellid: MCC, MNC, LAC, CI, ShortName config keys"""
		c.execute("""SELECT * FROM CONFIG WHERE KEYSTRING='%s'""" %cell_id[0])
		item = c.fetchone()
		print("%s = %s" %(item[0], item[1]))
		c.execute("""SELECT * FROM CONFIG WHERE KEYSTRING='%s'""" %cell_id[1])
		item = c.fetchone()
		print("%s = %s" %(item[0], item[1]))
		c.execute("""SELECT * FROM CONFIG WHERE KEYSTRING='%s'""" %cell_id[2])
		item = c.fetchone()
		print("%s = %s" %(item[0], item[1]))
		c.execute("""SELECT * FROM CONFIG WHERE KEYSTRING='%s'""" %cell_id[3])
		item = c.fetchone()
		print("%s  = %s" %(item[0], item[1]))
		c.execute("""SELECT * FROM CONFIG WHERE KEYSTRING='%s'""" %cell_id[4])
		item = c.fetchone()
		print("%s = %s" %(item[0], item[1]))
		c.execute("""SELECT * FROM CONFIG WHERE KEYSTRING='%s'""" %cell_id[5])
		item = c.fetchone()
		print("%s = %s" %(item[0], item[1]))
    else:
        """Display config key"""
        c.execute("""SELECT * FROM CONFIG WHERE KEYSTRING='%s'""" %(args[1]))
        item = c.fetchone()
        print("%s=%s\nComment:%s" %(item[0], item[1], item[4]))

def set(conn, c, param, value):
	if(param == "cellid"):
		c.execute("""UPDATE CONFIG set VALUESTRING='%s' WHERE KEYSTRING='GSM.Identity.MCC'"""%(232))
		c.execute("""UPDATE CONFIG set VALUESTRING='%s' WHERE KEYSTRING='GSM.Identity.MNC'"""%(value))
		print("Setting\nMCC = 232")
		print("MNC = %s"%(value))
	else:
		c.execute("""UPDATE CONFIG set VALUESTRING='%s' WHERE KEYSTRING='%s'"""%(value, param))
		print("Setting\n%s=%s"%(param, value))
 
def help():
    print("\nDescription:")
    print("   Script is used to manage the OpenBTS configuration database.")
    print("   located usually in /etc/OpenBTS/OpenBTS.db")
    print("\nUsing the script:")
    print("   To display all config keys, or a specific key:")
    print("      openbtsconf.py show all")
    print("      openbtsconf.py show cellid")
    print("      openbtsconf.py show <KEYSTRING>\n")
    print("   To set a key value:")
    print("      openbtsconf.py set cellid <VALUESTRING>\n")   
    print("      openbtsconf.py set <KEYSTRING> <VALUESTRING>\n")
    print("More imformation:")
    print("   http://docs.imatte.cz/temata/konvergence-openims-openbts\n")
    print(""
	"Austria\n"
	"MCC 	MNC 	Network 		Operator or brand name 	Status\n"
	"232 	1   	A1 Telekom Austria 	A1 			Operational\n"
	"232 	2   	A1 Telekom Austria 	A1 			Operational\n"
	"232 	3   	T-Mobile 		T-Mobile 		Operational\n"
	"232 	5   	Orange 			Orange 			Operational\n"
	"232 	6   				Orange 			Operational\n"
	"232 	7   	tele.ring 		Tele.ring 		Operational\n"
	"232 	9   	A1 Telekom Austria 	A1 			Operational\n"
	"232 	10   	Hutchison 3G Austria 	3 (Drei) 		Operational\n"
	"232 	11   	A1 Telekom Austria 	Bob 			Operational\n"
	"232 	12   	Yesss (Orange) 		Yesss 			Operational\n"
	"232 	14   	Hutchison 3G Austria 	3 (Drei) 		Operational\n"
	"232 	15   	Barablu Mobile Ltd 	Barablu 		Operational\n"
	"232 	91   	OBB 			GSM-R A 		Inactive  \n")

def main():
    # Default location of OpenBTS config database (/etc/OpenBTS/OpenBTS.db)
    db_path = "/etc/OpenBTS/OpenBTS.db"
    conn = sqlite3.connect(db_path)
    c = conn.cursor()   
    print "Database: ", db_path
    options, args = getopt.getopt(sys.argv[1:], "")
     
    if(len(args) == 0):
        help()
    elif(args[0] == "help"):
        help()
    elif(args[0] == "set" and len(args) == 3):
        param = args[1]
        value = args[2]
        set(conn, c, param, value)
    elif(args[0] == "show" and len(args) == 2):
        show(conn, c, args)
    else:
        help()
     
    conn.commit()
    conn.close()   
         
if __name__ == "__main__":
    main()

