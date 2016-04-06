; Bits and pieces for Asterisk
; Im sure if you have a server with a public IP you will have seen calls on the console screen where the call is to a destination but the callers is exten@yourserver . Well this little bit of dialplan at the end of you default sip context should catch them and log them with the ip of the originating server.
; http://forums.asterisk.org/viewtopic.php?f=1&t=86557
;bit of dialplan to catch anon callers ip addresses
exten => _X.,1,Noop(Dead calls rising)
exten => _X.,n,Set(uri=${SIPCHANINFO(uri)})
exten => _X.,n,Verbose(3,Unknown call from ${uri} to ${EXTEN})
exten => _X.,n,System(echo "[${STRFTIME(${EPOCH},,%b %d %H:%M:%S)}] SECURITY[] Unknown Call from ${CALLERIDNUM} to ${EXTEN} IPdetails ${CHANNEL(uri)}" >> /var/log/asterisk/sipsec.log)
exten => _X.,n,Hangup()
; so you get now
; [May 1 00:11:06] SECURITY[] Unknown Call from  to 900441516014742 IPdetails sip:101@37.75.209.113:21896

;###################  The stuff below is not a part of this dialplan. It is just notes for future use.     #############
;#######################################################################################################################
[bits-and-pieces]

;jump to a context with a particular telephone number
same => n,GotoIf($[DIALPLAN_EXISTS(test,${EXTEN},1)]?test,${EXTEN},1)
[fix-callerid]
; Convert incoming CID from IMSIxxxx into a number
; https://github.com/DanielO/asterisk-openbts/blob/master/extensions.conf#L45
exten => s,1,Verbose(IMSI ${CALLERID(num)})
same => n,Set(CALLERID(num)=${ODBC_SQL(select callerid from sip_buddies where name = \"${CALLERID(num)}\")})
same => n,Verbose(Converted calling number to ${CALLERID(num)})
same => n,Return()


;http://manuals.loway.ch/QM_AdvancedConfig-chunked/ar01s14.html
;exten => 999,n,Set(MONITOR_FILENAME=/recordings/${STRFTIME(${EPOCH},,%Y-%m/%d)}/audio-${UNIQUEID}.wav)
;creates a new wav folder every day
