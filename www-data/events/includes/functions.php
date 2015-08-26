<?PHP
/*
 Copyright (C) 2006 Earl C. Terwilliger
 Email contact: earl@micpc.com

    This file is part of The Asterisk EVENT MONITOR WEB/PHP Interface.

    These files are free software; you can redistribute them and/or modify
    them under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    These programs are distributed in the hope that they will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
*/
?>
<script language='JavaScript'>
<!--    
function change_id(obj,nid) { obj.id=nid; }
// -->  
</script>

<?PHP
function commands_buttons() {
?>
<script language='JavaScript'>
<!--    
function update_function(func) { 
  document.fform.command.value = func;
  chan = document.fform.channel.value;
  if (func == 'chanspy') {
    ref  = 'cmdprompt.php?action=originate';
    ref += '&Channel=' + chan;
    ref += '&WaitTime=30';
    ref += '&Application=ChanSpy';
    ref += '&CallerID=' + '"Web Call"';
    ref += '&Data=' + chan;
    location.href=ref;
    return;
  }
  if (func == 'events') {
    ref = 'eventlist.php?chan=' + chan;
    location.href=ref;
    return;
  }
  ref = 'cmdprompt.php?&action=' + func;                      
  if (func == 'hangup') {
    if (chan != "") ref += '&channel=' + chan;                      
    else            ref += '&prompt=channel';                      
  }
  location.href=ref;
}

// -->  
</script>

    <table width=100% border=0>
     <tr>
        <td align=center><button name=ChanSpy     onclick="update_function('chanspy')"
                                     onMouseOver='change_id(this,"ButtonY")'
                                     onMouseOut='change_id(this,"ButtonF")'
              id=ButtonF>ChanSpy     </button></td>
        <td align=center><button name=Hangup      onclick="update_function('hangup')"
                                     onMouseOver='change_id(this,"ButtonY")'
                                     onMouseOut='change_id(this,"ButtonF")'
              id=ButtonF>Hangup      </button></td>
        <td align=center><button name=Transfer    onclick="update_function('transfer')" 
                                     onMouseOver='change_id(this,"ButtonY")'
                                     onMouseOut='change_id(this,"ButtonF")'
              id=ButtonF>Transfer    </button></td>
        <td align=center><button name=Monitor     onclick="update_function('monitor')" 
                                     onMouseOver='change_id(this,"ButtonY")'
                                     onMouseOut='change_id(this,"ButtonF")'
              id=ButtonF>Monitor     </button></td>
        <td align=center><button name=StopMonitor onclick="update_function('stopmonitor')"
                                     onMouseOver='change_id(this,"ButtonY")'
                                     onMouseOut='change_id(this,"ButtonF")'
              id=ButtonF>StopMonitor </button></td>
     </tr><tr>
        <td align=center><button name=Redirect    onclick="update_function('redirect')"
                                     onMouseOver='change_id(this,"ButtonY")'
                                     onMouseOut='change_id(this,"ButtonF")'
              id=ButtonF>Redirect    </button></td>
        <td align=center><button name=ZapHangup   onclick="update_function('zaphangup')"
                                     onMouseOver='change_id(this,"ButtonY")'
                                     onMouseOut='change_id(this,"ButtonF")'
              id=ButtonF>ZapHangup   </button></td>
        <td align=center><button name=ZapTransfer onclick="update_function('zaptransfer')"
                                     onMouseOver='change_id(this,"ButtonY")'
                                     onMouseOut='change_id(this,"ButtonF")'
              id=ButtonF>ZapTransfer </button></td>
        <td align=center><button name=Events      onclick="update_function('events')"
                                     onMouseOver='change_id(this,"ButtonY")'
                                     onMouseOut='change_id(this,"ButtonF")'
              id=ButtonF>Events      </button></td>
     </tr>
    </table>
<?PHP
}

// $tdnowrap  = "<td nowrap onMouseover=\"this.style.backgroundColor='yellow'\"";
// $tdnowrap .= " onMouseout=\"this.style.backgroundColor='white'\">";

   $tdnowrap = "<td nowrap>";

 function buttons($pattern) {
   echo "<br>\n";
   echo "<button id=Sel name='MONITOR' ";
   echo " onMouseOver='change_id(this,\"ButtonY\")' ";
   echo " onMouseOut='change_id(this,\"Sel\")' ";
   echo " onClick='location.href=\"index.php\"'>MONITOR</button>\n";
   echo "&nbsp;&nbsp;&nbsp;&nbsp;";
   echo "<button id=Sel name='AGENTS' ";
   echo " onMouseOver='change_id(this,\"ButtonY\")' ";
   echo " onMouseOut='change_id(this,\"Sel\")' ";
   echo " onClick='location.href=\"agents_index.php\"'>AGENTS</button>\n";
   echo "&nbsp;&nbsp;&nbsp;&nbsp;";
   echo "<button id=Sel name='BACK' ";
   echo " onMouseOver='change_id(this,\"ButtonY\")' ";
   echo " onMouseOut='change_id(this,\"Sel\")' ";
   echo " onClick='javascript:history.back()'>GO BACK</button>\n";
   if ($pattern != "") {
     echo "&nbsp;&nbsp;&nbsp;&nbsp;";
     echo "<button id=Sel name='CDR RECORD' ";
     echo " onMouseOver='change_id(this,\"ButtonY\")' ";
     echo " onMouseOut='change_id(this,\"Sel\")' ";
     echo "onClick='location.href=\"cdr_list.php?pattern=$pattern\"";
     echo "'>CDR RECORD</button>\n";
   }
   echo "<br><br>\n";
   echo "<table border=0>\n";
   echo "<tr><td nowrap>";
   echo "<form method=post action=cdr_list.php>\n";
   echo "<input id=Sel ";
   echo " onMouseOver='change_id(this,\"ButtonY\")' ";
   echo " onMouseOut='change_id(this,\"Sel\")' ";
   echo " type=submit value='CDR SEARCH'>\n";
   echo "&nbsp;&nbsp;";
   echo "<input type=text name=pattern value='' size=20>\n";
   echo "</form>\n";
   echo "</td>\n";
   echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>\n";
   echo "<td nowrap>";
   echo "<form method=post action=eventlist.php>\n";
   echo "<input id=Sel ";
   echo " onMouseOver='change_id(this,\"ButtonY\")' ";
   echo " onMouseOut='change_id(this,\"Sel\")' ";
   echo " type=submit value='EVENT SEARCH'>\n";
   echo "&nbsp;&nbsp;\n";
   echo "<input type=text name=search value='' size=20>\n";
   echo "</form>\n";
   echo "</td>\n";
   echo "</tr></table>\n";
 }
?>
