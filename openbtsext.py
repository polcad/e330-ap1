#!/usr/bin/python
"""
Scripts are used for inserting, deleting, and displaying data from database
SQlite /var/lib/asterisk/sqlite3dir/sqlite3.db,
which contains a mapping of VoIP extensions to the caller id
 
"""
import sys
import sqlite3, getopt
 
def insert(conn, c, exten, dial):
    """
    Add entry to the table: dialdata_table, sip_buddies
    """
    c.execute("SELECT exten FROM dialdata_table WHERE exten='%s' or dial='%s'"
              %(exten, dial))
    result = c.fetchone()
     
    if result:
        print("Record exten=<%s> or dial=<%s> already exists"
              %(exten, dial))
    else:       
        c.execute("""INSERT INTO dialdata_table (exten, dial)
            VALUES('%s', '%s')"""
            %(exten, dial))
         
        id = getID(conn, c, exten)
        c.execute("""INSERT INTO sip_buddies VALUES(%s,'%s','phones',
            'allowed_not_screened',NULL,NULL,NULL,NULL,NULL,NULL,'dynamic',
            'no','friend',NULL,NULL,NULL,'%s','0.0.0.0','info',NULL,NULL,NULL,
            NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'all','gsm',NULL,
            '127.0.0.1',5062,'%s',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,
            NULL,'yes','yes','yes','no',NULL,'no',NULL,'yes','accept',1800,90,
            'uas',NULL,NULL,NULL,'yes',500,NULL,120,NULL,NULL,0,NULL,0,NULL,
            'yes','no',NULL,NULL,NULL,NULL,0,0,NULL,NULL,NULL,NULL,0,0,0,1,0,
            NULL)"""
            %(id, dial, exten, dial))  
         
        print("Record exten=<%s> and dial=<%s> added"%(exten, dial))
     
def delete(conn, c, exten):
    """
    Delete record from tables: dialdata_table, sip_buddies
    """
    c.execute("SELECT exten FROM dialdata_table WHERE exten='%s'"%(exten))
    result = c.fetchone()
     
    if result:
        id = getID(conn, c, exten)
        c.execute("DELETE FROM dialdata_table WHERE exten='%s'"%(exten))        
        c.execute("DELETE FROM sip_buddies WHERE id='%s'"%(id)) 
        print("Record exten=<%s> deleted"%(exten))
    else:
        print("Record exten=<%s> does not exist in the database."%(exten))
 
def show(conn, c, args):
    """
    Display data from tables: dialdata_table, sip_buddies
    """
    if(args[1] == "all"):
        """Display all records from dialdata_table and sip_buddies tables"""
        c.execute("""SELECT D.id, D.exten, D.dial, S.hardware, S.context
                    FROM dialdata_table D, sip_buddies S WHERE D.id=S.id""")
        for item in c.fetchall():
            print("ID(%s) exten=<%s> dial=%s Hardware=<%s> context=<%s>"
                  %(item[0], item[1], item[2], item[3], item[4]))       
    else:
        """A listing of all records in the database"""
        c.execute("""SELECT D.id, D.exten, D.dial, S.context
                    FROM dialdata_table D, sip_buddies S
                    WHERE D.id=S.id and D.exten='%s'"""
                    %(args[1]))
        item = c.fetchone()
        print("ID(%s) exten=<%s> dial=%s context=<%s>"
              %(item[0], item[1], item[2], item[3]))
 
def getID(conn, c, exten):
    """
    Get ID from table : dialdata_table
    """
    c.execute("SELECT id FROM dialdata_table WHERE exten='%s'"%(exten))
    return c.fetchone()[0]
  
def help():
    """
    Vypis navodu k pouziti skriptu
    """
    print("\nDescription:")
    print("   Script is used to manage the database, which is used by OpenBTS and Asterisk")
    print("   to map VoIP extension to dial extension")
    print("   Database located in: /var/lib/asterisk/sqlite3dir/sqlite3.db")
    print("\nUsing the script:")
    print("   Listing of all database records, or a specific record")
    print("      openbtsext.py show all")
    print("      openbtsext.py show <exten>\n")
    print("   Adding a nr")   
    print("      openbtsext.py insert <exten> <dial>\n")
    print("   Deleting a nr")
    print("      openbtsext.py delete <exten>\n")
    print("More information:")
    print("   http://docs.imatte.cz/temata/konvergence-openims-openbts\n")
 
def main():
    # Database path (/var/lib/asterisk/sqlite3dir/sqlite3.db)
    db_path = "sqlite3.db"
    db_path = "/var/lib/asterisk/sqlite3dir/sqlite3.db"
    print "Database: ", db_path
    conn = sqlite3.connect(db_path)
    c = conn.cursor()   
     
    options, args = getopt.getopt(sys.argv[1:], "")
     
    if(len(args) == 0):
        help()
    elif(args[0] == "help"):
        help()
    elif(args[0] == "insert" and len(args) == 3):
        number = args[1]
        imsi = args[2]
        insert(conn, c, number, imsi)
    elif(args[0] == "delete" and len(args) == 2):
        number = args[1]
        delete(conn, c, number)
    elif(args[0] == "show" and len(args) == 2):
        show(conn, c, args)
    else:
        help()
     
    conn.commit()
    conn.close()   
         
if __name__ == "__main__":
    main()
