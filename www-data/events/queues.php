<?php 
/*
 Copyright (C) 2006-2009 Earl C. Terwilliger
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
if (!isset($_SESSION['Login'])) { header("Location: index.php"); exit(); }

ob_implicit_flush(false);

$agents = array();
$curr_agent = "";
$better_status = array(	'AGENT_UNKNOWN' 	=> 'Unknown',
			'AGENT_IDLE' 		=> 'Idle',
			'AGENT_ONCALL' 		=> 'On Call',
			'AGENT_LOGGEDOFF' 	=> 'Not Logged In' );

include('includes/manager_login.php');
fputs($socket, "Action: Agents\r\n\r\n");
fputs($socket, "Action: Logoff\r\n\r\n");

 while(!feof($socket)) {
	$info = fscanf($socket, "%s\t%s\r\n");
	switch($info[0]) {
		case "Agent:":
			$curr_agent = $info[1];
			$agents[$curr_agent] = array();
			break;
		case "Name:":
			$agents[$curr_agent]['Name'] = $info[1];
			break;
		case "Status:":
			$agents[$curr_agent]['Status'] = $better_status[$info[1]];
			break;
		case "LoggedInChan:":
			$agents[$curr_agent]['LoggedInChan'] = $info[1];
			break;
		case "LoggedInTime:":
			if($info[1] != "0") {
				$agents[$curr_agent]['LoggedInTime'] = date("D, M d Y g:ia", $info[1]);
			} else {
				$agents[$curr_agent]['LoggedInTime'] = "n/a";
			}
			break;
		case "TalkingTo:":
			$agents[$curr_agent]['TalkingTo'] = $info[1];
			break;
		default:
			break;
		}
  }

  fclose($socket);

  print "<html><head><title>Queue and Agent Status</title>";
  print "<META NAME=\"Copyright\"     CONTENT=\"Copyright (C) 2006-2009 Earl Terwilliger earl@micpc.com All Rights reserved\">\n";
  print "  <META NAME=\"Description\"   CONTENT=\"PHP script '";
  print $_SERVER['PHP_SELF'];
  print "' Copyright (C) 2006-2009 by Earl C. Terwilliger earl@micpc.com\">\n";
  print "  <LINK REL=\"SHORTCUT ICON\" HREF=\"http://$_SERVER[SERVER_NAME]/favicon.ico\">\n";
  print "  <link rel=\"stylesheet\" type=\"text/css\" href=\"includes/style.css\">\n";
  print "<META HTTP-EQUIV=\"Pragma\"  CONTENT=\"no-cache\">";
  print "<META HTTP-EQUIV=\"Expires\" CONTENT=\"-1\">";
  print "<META HTTP-EQUIV=\"REFRESH\" CONTENT=\"10\">";
  print "</head>\n<body>\n";
  print "<center>";
?>
  <div id="divheader">
    <table width=100% border=0>
     <tr>
          <td nowrap>
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
          </td>
          <td><img src=images/em-logo.png border=0></img></td>
          <td>
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
    </table>
   <br>
  </div>
<?PHP                                                                   
//  print "<H1>AGENT STATUS</H1><br>";
  print "<table width=\"800px\" border=\"1\">\n";
  print "  <tr><th>Agent</th><th>Name</th><th>Channel</th><th>Status</th><th>Talking To</th><th>Login Time</th></tr>\n";

  foreach( $agents as $agent=>$curr ) {
	print "  <tr>\n    <td>" . $agent . "</td>\n";
	print "    <td>" . $curr['Name'] . "</td>\n";
	print "    <td>" . $curr['LoggedInChan'] . "</td>\n";
	print "    <td>";
        if (strtolower($curr['Status']) == "not logged in") 
          print "<a href=agentlogin.php?agent="  . $agent . ">";
        else
          print "<a href=agentlogoff.php?agent=" . $agent . ">";
        print $curr['Status']; 
        print "</a>";
	print "</td>\n";
	print "    <td>" . $curr['TalkingTo'] . "</td>\n";
	print "    <td>" . $curr['LoggedInTime'] . "</td>\n  </tr>\n";
  }
  print "</table>\n";

//  print "<H1>QUEUE STATUS</H1><br>";
  print "</center>\n";

//  include('includes/manager_login.php');
  $socket = fsockopen($astip,$astport,$errno,$errstr,$timeout);
  if (!$socket) {
    echo "<pre>";
    echo "$errstr ($errno)<br>\n";
    echo "</pre>";
    exit(99);
  }

  fputs($socket, "Action: Login\r\n");
  fputs($socket, "UserName: $astmanager\r\n");
  fputs($socket, "Secret: $astpassword\r\n\r\n");

  fputs($socket, "Action: Queues\r\n\r\n");
  fputs($socket, "Action: Logoff\r\n\r\n");

  $data = "";
  while (!feof($socket)) $data .= fread($socket, 4096);
  $lines = explode("\n",$data);
  while (list($key, $val) = each($lines)) {
    if (substr($val,0,8) == "Asterisk") continue;
    if (substr($val,0,8) == "Message:") continue;
    if (substr($val,0,9) == "Response:") continue;
    echo "$val<br>\n";
  }

  fclose($socket);
  print "</body>\n</html>\n";
?>
