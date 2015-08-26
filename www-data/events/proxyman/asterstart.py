#!/bin/env python

#
# Copyright (C) 2006-2011 Earl Terwilliger
#               EMAIL: earl@micpc.com
#
# Version 2.0  1-17-2011

import sys,os,string,time

Asterisk = "asterisk -p"
ProxyMan = "/opt/asterisk/scripts/ProxyMan.py"

def get_processes():
  output = []
  ps = os.popen('ps -eo pid,command')
  ps.readline()
  for line in ps: output.append(line.lstrip()[:-1])
  ps.close()
  return output

def check_process(cmd):
  cnt = 0
  ids = get_processes()
  for i in range(len(ids)):
    if string.find(ids[i],cmd) != -1: cnt += 1
  return cnt

def kill_process(cmd):
  ids = get_processes()
  for i in range(len(ids)):
    if string.find(ids[i],cmd) != -1:
      killcmd = "kill -9 " + str(ids[i][0:4]).strip()
      print killcmd
      os.system(killcmd)

def start_process(cmd):
  cnt = check_process(cmd)
  if cnt == 0: 
    os.system(cmd)
    print "Started: ",cmd

if __name__ == '__main__':
  cnt = check_process(sys.argv[0])
  if (cnt > 1):
    print "Exiting.. already running!"
    sys.exit(0)
  pid = os.fork()
  if pid: sys.exit(0)
  while(1):
    cnt = check_process(Asterisk)
    if cnt == 0: 
      kill_process(ProxyMan)
      start_process(Asterisk)
      time.sleep(1)
      start_process(ProxyMan)
    cnt = check_process(ProxyMan)
    print "cnt= ",cnt
    if cnt == 0: 
      start_process(ProxyMan)
    time.sleep(10)
