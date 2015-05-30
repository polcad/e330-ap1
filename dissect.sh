#!/bin/bash
#2015-01-11 rev0.1
tshark -T pdml -i lo -Y '!icmp && gsmtap' > arfcn-cap.xml
