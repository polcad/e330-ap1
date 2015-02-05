; Bits and pieces for Asterisk
; Im sure if you have a server with a public IP you will have seen calls on the console screen where the call is to a destination but the callers is exten@yourserver . Well this little bit of dialplan at the end of you default sip context should catch them and log them with the ip of the originating server.
; http://forums.asterisk.org/viewtopic.php?f=1&t=86557
exten => _X.,1,Noop(Dead calls rising)
exten => _X.,n,Set(uri=${CHANNEL(uri)})
exten => _X.,n,Verbose(3,Unknown call from ${uri} to ${EXTEN})
exten => _X.,n,System(echo "[${STRFTIME(${EPOCH},,%b %d %H:%M:%S)}] SECURITY[] Unknown Call from ${CALLERIDNUM} to ${EXTEN} IPdetails ${uri}" >> /var/log/asterisk/sipsec.log)
exten => _X.,n,Hangup()
; so you get now
; [May 1 00:11:06] SECURITY[] Unknown Call from  to 900441516014742 IPdetails sip:101@37.75.209.113:21896
