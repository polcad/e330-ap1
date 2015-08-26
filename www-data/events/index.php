<?php
/*
 Copyright (C) 2006-2011 Earl C. Terwilliger
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
  session_start();
  if (!isset($_SESSION['Login'])) header("Location: login.php");
  if (!isset($_SESSION['eventid'])) {
    $login = $_SESSION['Login'];
    $_SESSION = array();
    $_SESSION['Login']       = $login;
    $_SESSION['agentid']     = 0;
    $_SESSION['agents']      = array();
    $_SESSION['agentstatus'] = array();
    $_SESSION['channels']    = array();
    $_SESSION['chanstatus']  = array();
    $_SESSION['phones']      = array();
    $_SESSION['sipstatus']   = array();
  }
  $_SESSION['channelid']   = 0;
  $_SESSION['eventid']     = 0;
  $_SESSION['aeventid']    = 0;
  $_SESSION['sipid']       = 0;
  include("includes/header.php");
?>
<script language='JavaScript'>
<!--

var ebusy = 0;
var cbusy = 0;
var sbusy = 0;

function change_id(obj,nid) { obj.id=nid; } 

function createRequestObject() {
 var request;
 var browser = navigator.appName;
 if(browser == "Microsoft Internet Explorer") request = new ActiveXObject("Microsoft.XMLHTTP");
 else                                         request = new XMLHttpRequest();
 return request;
}

var httpe = createRequestObject();
var httpc = createRequestObject();
var https = createRequestObject();

function handleResponsee() {
  if (httpe.readyState == 4) {
    var response = httpe.responseText;
    var pos      = response.indexOf('|');
    if (pos != -1) {
      divid = "div" + response.substr(0,pos);
      document.getElementById(divid).innerHTML = response.substr(pos+1);
      sndReqc();
      sndReqs();
    }
    ebusy = 0;
  }
}

function handleResponsec() {
  if (httpc.readyState == 4) {
    var response = httpc.responseText;
    var pos      = response.indexOf('|');
    if (pos != -1) {
      divid = "div" + response.substr(0,pos);
      document.getElementById(divid).innerHTML = response.substr(pos+1);
    }
    cbusy = 0;
  }
}

function handleResponses() {
  if (https.readyState == 4) {
    var response = https.responseText;
    var pos      = response.indexOf('|');
    if (pos != -1) {
      divid = "div" + response.substr(0,pos);
      document.getElementById(divid).innerHTML = response.substr(pos+1);
    }
    sbusy = 0;
  }
}

function sndReqe() {
  if (ebusy == 1) return;
  httpe.open('get', 'events.php?action=events');
  httpe.onreadystatechange = handleResponsee;
  httpe.send(null);
  ebusy = 1;
}

function sndReqc() {
  if (cbusy == 1) return;
  httpc.open('get', 'channels.php?action=channels');
  httpc.onreadystatechange = handleResponsec;
  httpc.send(null);
  cbusy = 1;
}

function sndReqs() {
  if (sbusy == 1) return;
  https.open('get', 'sip.php?action=sip');
  https.onreadystatechange = handleResponses
  https.send(null);
  sbusy = 1;
}

function eventlist_channel(chan) {
  ref = 'eventlist.php?chan=' + chan;
  location.href=ref;
  return
}

function send_request() {  sndReqe();  sndReqc(); sndReqs(); }

window.setInterval("send_request()",2000);

// -->
</script>
<body onload=send_request();>
<?php
 if (count($_SESSION['phones']) == 0) {
   $fname = "/etc/asterisk/sip.conf";
   $fd = fopen($fname, "r");
   if ($fd) { 
     $user = array();
     $section = 0; 
     while (!feof ($fd)) {
       $buffer = fgets($fd, 4096);
       $l = trim($buffer);
       $pattern = '/^\s*\[(.*?)\]/';
       if (preg_match($pattern,$l,$matches)) $section = 1;
       if ($section) { 
         $pattern = '/^\s*(\w+)\s*=\s*(.+)\s*;?.*$/';
         if (preg_match($pattern,$l,$matches)) {
            if      ($matches[1] == "username")    { $user[] = "SIP/" . $matches[2]; $section = 0; }
            else if ($matches[1] == "defaultuser") { $user[] = "SIP/" . $matches[2]; $section = 0; }
            else if ($matches[1] == "regexten")    { $user[] = "SIP/" . $matches[2]; $section = 0; }
         }
       }
     }
     fclose ($fd); 
     $_SESSION['phones'] = $user;
   }
 }

/*
The naming convention of channels changes in asterisk versions. 
So, this whole section is commented out. The channels will appear on the WEB page dynamically as they are used. 
If you know the convention used on your system, change the code below and uncomment it.
*/

/*

 if (count($_SESSION['channels']) == 0) {
   $channels = array();
   $fname = "/etc/asterisk/dahdi-channels.conf";
//   $fname = "/etc/asterisk/chan_dahdi.conf";
   if (file_exists($fname)) {
     $chantype = "Dahdi";
     $fd = fopen($fname, "r");
     if ($fd) { 
       $group = "";
       while (!feof ($fd)) {
         $buffer = fgets($fd, 4096);
         $l = trim($buffer);
         $pattern = '/^\s*(\w+)\s*=\s*(.+)\s*;?.*$/';
         if (preg_match($pattern,$l,$matches)) {
            if ($matches[1] == "group") $group = $matches[2];
         }
         if ($group == "") continue;
         $pattern = '/^\s*(\w+)\s*=>\s*(.+)\s*;?.*$/';
         if (preg_match($pattern,$l,$matches)) {
            if ($matches[1] == "channel") {
               $channels[] = $chantype . "/g" . $group . "/" . $matches[2];
               $group = "";
            }
         }
       }
       fclose ($fd); 
     }
   }
   else {
     $fname = "/etc/asterisk/zapata.conf";
     $chantype = "Zap";
     if (file_exists($fname)) $fd = fopen($fname, "r");
     if ($fd) { 
       $section = 0;
       $group = "";
       $numchans = 0;
       while (!feof ($fd)) {
         $buffer = fgets($fd, 4096);
         $l = trim($buffer);
         $pattern = '/^\s*\[(.*?)\]/';
         if (preg_match($pattern,$l,$matches)) {
           if (strtolower($matches[1]) == "channels") { $section = 1; continue; }
         }
         if (!$section) continue;
         $pattern = '/^\s*(\w+)\s*=\s*(.+)\s*;?.*$/';
         if (preg_match($pattern,$l,$matches)) {
            if ($matches[1] == "group") {
              $group    = $matches[2];
              $numchans = 0;
            }
         }
         if ($group == "") continue;
         $pattern = '/^\s*(\w+)\s*=>\s*(.+)\s*;?.*$/';
         if (preg_match($pattern,$l,$matches)) {
            if ($matches[1] == "channel")    {
               $chans = split(",",$matches[2]);
               $c = count($chans);
               for ($i=0;$i<$c;++$i) {         
                 $range = split("-",$chans[$i]);
                 if (count($range) > 1) {
                   for ($r=$range[0];$r<=$range[1];++$r) {         
                     $numchans += 1;
                     $channels[] = $chantype . "/" . $group . "-" . $numchans;
                   }
                 }
                 else {
                   $numchans += 1;
                   $channels[] = $chantype . "/" . $group . "-" . $numchans;
                 }
               }
            }
         }
       }
       fclose ($fd); 
     }
   }
   $_SESSION['channels'] = $channels;
 }

*/

?>
 <center>

  <div id="divheader">
    <table width=100% border=0>
     <tr>
          <td colspan=5 nowrap align=center valign=bottom>
                  <button name=monitor id=ButtonA
                        onMouseOver='change_id(this,"ButtonYA")'
                        onClick='location.href="index.php"'
                        onMouseOut='change_id(this,"ButtonA")'
                  >MONITOR</button>
                     &nbsp;&nbsp;&nbsp;&nbsp;
                  <button name=agents id=ButtonA
                        onMouseOver='change_id(this,"ButtonYA")'
                        onClick='location.href="agents_index.php"'
                        onMouseOut='change_id(this,"ButtonA")'
                  >AGENTS</button>
                     &nbsp;&nbsp;&nbsp;&nbsp;
                  <button name=queues id=ButtonA
                        onMouseOver='change_id(this,"ButtonYA")'
                        onClick='location.href="queues.php"'
                        onMouseOut='change_id(this,"ButtonA")'
                  >QUEUES</button>
                     &nbsp;&nbsp;&nbsp;&nbsp;
                  <button name=login id=ButtonA
                        onMouseOver='change_id(this,"ButtonYA")'
                        onClick='location.href="login.php"'
                        onMouseOut='change_id(this,"ButtonA")'
                  >LOGIN</button>
                     &nbsp;&nbsp;&nbsp;&nbsp;
                  <button name=logoff id=ButtonA
                        onMouseOver='change_id(this,"ButtonYA")'
                        onClick='location.href="logoff.php"'
                        onMouseOut='change_id(this,"ButtonA")'
                  >LOGOFF</button>
          </td>
      </tr>
     <tr>
          <td nowrap> <button name=Unknown   id=ButtonB> &nbsp;UNKNOWN  &nbsp;</button>
          </td>
          <td nowrap> <button name=Available id=ButtonG> &nbsp;AVAILABLE&nbsp; </button>
          </td>
          <td><img src=images/em-logo.png border=0></img></td>
          <td nowrap> <button name=Inuse     id=ButtonR> &nbsp; ACTIVE &nbsp; </button>
          </td>
          <td nowrap> <button name=Unavail   id=ButtonU> &nbsp;UNAVAILABLE&nbsp; </button>
          </td>
      </tr>
    </table>
    <br>
  </div>

  <div id="divsip"     ><br><br><H2>Checking SIP Info    </H2></div>

  <div id="divchannels"><br><br><H2>Checking Channel Info</H2></div>

  <div id="divevents"  ><br><br><H2>Checking Event Info  </H2></div>

 </center>

</body>
</html>
