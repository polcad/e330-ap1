#!/usr/bin/python
"""
This script is used to configure OpenBTS. The configuration is stored in a SQLite3 database.
SQlite /etc/OpenBTS/OpenBTS.db.

"""
import sys
import sqlite3, getopt
 
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
    else:
        """Display config key"""
        c.execute("""SELECT *
                    FROM CONFIG
                    WHERE KEYSTRING='%s'"""
                    %(args[1]))
        item = c.fetchone()
        print("%s=%s\nComment:%s"
              %(item[0], item[1], item[4]))
 
def set(conn, c, param, value):
    c.execute("""UPDATE CONFIG set VALUESTRING='%s' WHERE KEYSTRING='%s'"""%(value, param))
    print("Setting\n%s=%s"%(param, value))
 
def help():
    """
    Vypis navodu k pouziti skriptu
    """
    print("\nDescription:")
    print("   Script is used to manage the OpenBTS configuration database.")
    print("   located usually in /etc/OpenBTS/OpenBTS.db")
    print("\nPouziti:")
    print("   To display all config keys, or a specific key:")
    print("      openbtsconf.py show all")
    print("      openbtsconf.py show <KEYSTRING>\n")
    print("   To set a key value:")   
    print("      openbtsconf.py set <KEYSTRING> <VALUESTRING>\n")
    print("More imformation:")
    print("   http://docs.imatte.cz/temata/konvergence-openims-openbts\n")
 
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

