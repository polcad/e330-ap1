#!/bin/bash
# This script will run Wireshark
# with filters set for monitoring GSM, gsmtap. Also with RTL dongle.
wireshark -k -Y '!icmp && gsmtap' -i lo
