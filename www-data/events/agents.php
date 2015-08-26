<?PHP
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
 if (!isset($_SESSION['Login'])) exit();

 $agentid = $_SESSION['agentid'];
 $action  = $_GET['action'];
 $agents  = $_SESSION['agents'];
 $status  = $_SESSION['agentstatus'];

 include("includes/db_connect.php");

 $query  = "SELECT max(id) FROM $tn";
 $result = mysql_query($query);
 $erno = mysql_errno();
 $err  = mysql_error();
 if ($erno <> 0) die($action."|".$query."<br>".$err);
 $list  = mysql_fetch_assoc($result);
 $maxid = $list['max(id)'];

 if ($maxid == $agentid) { echo $action; mysql_close($mylink); exit(); }

 $ct = date('Y-m-d',time());
 $query  = "SELECT * FROM $tn WHERE ";
 $query .= " (event like 'Event: Agent%') ";
 $query .= " and (id > '$agentid') ";
 $query .= " and (timestamp > '$ct') ";
 $query .= " order by id asc";

 $result = mysql_query($query);
 $erno = mysql_errno();
 $err  = mysql_error();
 if ($erno <> 0) die($action."|".$query."<br>".$err);

 $count = mysql_num_rows($result);
 $numfields = mysql_num_fields($result);
 $rows = 0;

 while($rows < $count) {
   $rows++;
   $list = mysql_fetch_assoc($result);
   $data  = trim($list['event']);
   list($event,$event_val,$ev,$priv,$priv_val,$pv,$agent,$agent_val,$av,$extra) = split(" ", $data, 10);
   if ( ($event_val == "Agentcallbacklogoff") ||
        ($event_val == "Agentlogoff") ) { 
     if (!in_array($agent_val,$agents)) $agents[] = $agent_val;
     $status[$agent_val] = 0;
   }
   if ( ($event_val == "Agentcallbacklogin") || 
        ($event_val == "Agentlogin") ) { 
     if (!in_array($agent_val,$agents)) $agents[] = $agent_val;
     $status[$agent_val] = 1;
   }
 }

 $action .= "|"; 
 $action .= '<table width="100%" cellpadding=2 cellspacing=2 border=0>';
 $action .= '<tr>';
 $c = count($agents);
 for ($i=0;$i<$c;++$i)  {
   if ( (($i %  6) == 0) && ($i != 0) ) $action .= "</tr><tr>";
   $action .= "<td align=center><button name='s" . $agents[$i];
   $action .= "' onMouseOver='change_id(this,\"ButtonY\")'\n";
   $action .= "  onClick='eventlist_agent(\"" . $agents[$i] . "\")'\n";
   if (isset($status[$agents[$i]])) {
     if ($status[$agents[$i]] == 0) { 
       $action .= "  onMouseOut='change_id(this,\"ButtonU\")'\n";
       $action .= "  id='ButtonU'>\n";
     }
     else { 
       if ($status[$agents[$i]] == 1) { 
         $action .= "  onMouseOut='change_id(this,\"ButtonG\")'\n";
         $action .= "  id='ButtonG'>\n";
       }
       else {
         $action .= "  onMouseOut='change_id(this,\"ButtonB\")'\n";
         $action .= "  id='ButtonB'>\n";
       }
     }
   }
   else {
     $action .= "  onMouseOut='change_id(this,\"ButtonB\")'\n";
     $action .= "  id='ButtonB'>\n";
   }
   $action .=  strtoupper($agents[$i]) . "</button>\n";
   $action .= "</td>\n";
 }
 $action .= '</tr></table><br>';

 $_SESSION['agentid']     = $maxid;
 $_SESSION['agents']      = $agents;
 $_SESSION['agentstatus'] = $status;
 mysql_close($mylink);
 echo $action; 
?>
