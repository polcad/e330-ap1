<?php
/*
 Copyright (C) 2006-2007 Earl C. Terwilliger
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
    $login                   = $_SESSION['Login'];
    $_SESSION                = array();
    $_SESSION['Login']       = $login;
    $_SESSION['agents']      = array();
    $_SESSION['agentstatus'] = array();
    $_SESSION['channels']    = array();
    $_SESSION['chanstatus']  = array();
    $_SESSION['phones']      = array();
    $_SESSION['sipstatus']   = array();
  }
  $_SESSION['agentid']     = 0;
  $_SESSION['eventid']     = 0;
  $_SESSION['aeventid']    = 0;
  $_SESSION['taskid']      = 0;
  include("includes/header.php");
?>
<script language='JavaScript'>
<!--

function change_id(obj,nid) { obj.id=nid; } 

var ebusy = 0;
var abusy = 0;
var tbusy = 0;

function createRequestObject() {
 var request;
 var browser = navigator.appName;
 if(browser == "Microsoft Internet Explorer") request = new ActiveXObject("Microsoft.XMLHTTP");
 else                                         request = new XMLHttpRequest();
 return request;
}

var httpe = createRequestObject();
var httpa = createRequestObject();
var httpt = createRequestObject();

function handleResponsee() {
  if (httpe.readyState == 4) {
    var response = httpe.responseText;
    var pos      = response.indexOf('|');
    if (pos != -1) {
      divid = "div" + response.substr(0,pos);
      document.getElementById(divid).innerHTML = response.substr(pos+1);
      sndReqa();
    }
    ebusy = 0;
  }
}

function handleResponset() {
  if (httpt.readyState == 4) {
    var response = httpt.responseText;
    var pos      = response.indexOf('|');
    if (pos != -1) { 
      window.open('agents_crm.php?id='+response.substr(pos+1),'AgentCRM','width=640,height=480,toolbar=yes,location=yes,directories=yes,status=yes,menubar=yes,scrollbars=yes,copyhistory=yes,resizable=yes',false);
    }
    tbusy = 0;
  }
}

function handleResponsea() {
  if (httpa.readyState == 4) {
    var response = httpa.responseText;
    var pos      = response.indexOf('|');
    if (pos != -1) {
      divid = "div" + response.substr(0,pos);
      document.getElementById(divid).innerHTML = response.substr(pos+1);
    }
    abusy = 0;
  }
}

function eventlist_agent(agent) {
  ref = 'eventlist.php?agent=' + agent;
  location.href=ref;
  return
}

function sndReqe() {
  if (ebusy == 1) return;
  httpe.open('get', 'agents_events.php?action=events');
  httpe.onreadystatechange = handleResponsee;
  httpe.send(null);
  ebusy = 1;
}

function sndReqa() {
  if (abusy == 1) return;
  httpa.open('get', 'agents.php?action=agents');
  httpa.onreadystatechange = handleResponsea;
  httpa.send(null);
  abusy = 1;
}

function sndReqt() {
  if (tbusy == 1) return;
  httpt.open('get', 'agents_tasks.php?action=tasks');
  httpt.onreadystatechange = handleResponset;
  httpt.send(null);
  tbusy = 1;
}

function send_request() {  sndReqe();  sndReqa(); sndReqt(); }

window.setInterval("send_request()",2000);

function open_new_window(id) {
  wc = 'eventlist.php?rec=' + id 
  new_window  = this.open(wc);
}

// -->
</script>
<body onload=send_request();>
<?php
 if (count($_SESSION['agents']) == 0) {
   $fname = "/etc/asterisk/agents.conf";
   $fd = fopen($fname, "r");
   if ($fd) { 
     $agents  = array();
     while (!feof ($fd)) {
       $buffer = fgets($fd, 4096);
       $l = trim($buffer);
       $pattern = '/^\s*\[(.*?)\]/';
       if (preg_match($pattern,$l,$matches)) {
         if (strtolower($matches[1]) == "agents") $agentsok = 1;
       }
       $pattern = '/^\s*(\w+)\s*=>\s*(.+)\s*;?.*$/';
       if (preg_match($pattern,$l,$matches)) {
          if ($matches[1] == "agent")  {
             $a = split(",",$matches[2]);
             $agents[] = $a[0];
          }
          continue;
       }
       $pattern = '/^\s*(\w+)\s*=\s*(.+)\s*;?.*$/';
       if (preg_match($pattern,$l,$matches)) {
          if ($matches[1] == "agent")  {
             $a = split(",",$matches[2]);
             $agents[] = $a[0];
          }
          continue;
       }
     }
     fclose ($fd); 
     $_SESSION['agents'] = $agents;
   }
 }
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

  <div id="divagents"  ><br><br><H2>Checking Agents Info</H2></div>

  <div id="divevents"  ><br><br><H2>Checking Event  Info</H2></div>

 </center>

</body>
</html>
