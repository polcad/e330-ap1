#!/bin/bash
#2015-01-11 rev0.1
tshark -T pdml -i lo -Y '!icmp && gsmtap' > /tmp/arfcn-cap.xml
