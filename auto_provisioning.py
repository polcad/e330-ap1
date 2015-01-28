#!/usr/bin/env python
#
import datetime
import time
import os

logfile_path="path_to_openbts/apps/test.out"
sipConf_path="/etc/asterisk/sip.conf"
extConf_path="/etc/asterisk/extensions.conf"



def updateSIPConfig(imsi, number): # returns false if entry already exists, true if new entry has been made
    if imsi > 0:
	sipConf = open(sipConf_path)
	for line in sipConf:
	    if line.find("[IMSI" + str(imsi) + "]") >= 0:
		sipConf.close()
		return False
	sipConf.close()
	sipConf = open(sipConf_path,'a')
	sipConf.write("#Auto provisioning on " + str(datetime.date.today())+ "\n")
	sipConf.write("[IMSI" + str(imsi) + "]\n")
	sipConf.write("callerid=" + str(number) + "\n")
	sipConf.write("canreinvite=no\n")
	sipConf.write("type=friend\n")
	sipConf.write("context=sip-external\n")
	sipConf.write("allow=gsm\n")
	sipConf.write("host=dynamic\n\n")
        sipConf.close()
	print "Updated IMSI in SIP config: " + str(imsi)
	return True


def updateEXTConfig(imsi): # returns false if entry already exists, true if new entry has been made
    number = 2010
    if imsi > 0:
	extConf = open(extConf_path)
	for line in extConf:
	    if line.find(",IMSI" + str(imsi) + ")") >= 0:
		extConf.close()
		return False
	    #search for last provisioned phone number
	    if line.find("exten => ") >= 0 and line.find("IMSI") >= 0: # if we have a GSM record already (else, this script will start with number 2010!)
		index = line.find("exten => ") + 9
		number = int(line[index:index+4]) + 1
	extConf.close()
	extConf = open(extConf_path, 'a')
	extConf.write("\nexten => " + str(number) + ",1,Macro(dialSIP,IMSI" + str(imsi) + ")")
	extConf.close()
	print "Updated IMSI in EXT config: " + str(imsi) + " with caller number " + str(number)
	updateSIPConfig(imsi, number)
	os.system("/etc/init.d/asterisk restart")
	return True 


def evaluateLogFile():
    logfile = open(logfile_path)
    for line in logfile:
        if line.find("registration ALLOWED: IMSI=") > 0:
            index=line.find("registration ALLOWED: IMSI=")
            imsi=line[index+27:len(line)-1]
	    updateEXTConfig(imsi)

    logfile.close()


# --------------------------------- #
# Let's start here!                 #
# --------------------------------- #
print " "
print "#------------------------------------------------------#"
print "You are using the automated provisioning system."
print "New mobile clients will be provisioned with a new caller number automatically."
print "***written by Markus Schafroth, 2010. m.schafroth@gmx.ch***"
print "#------------------------------------------------------#"
print ""

while 1:
    evaluateLogFile()
    time.sleep(10)
