<?PHP
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
 if (!isset ($_SESSION)) session_start();
 if (!isset($_SESSION['sipid'])) exit();

 $sipid  = $_SESSION['sipid'];
 $action = $_GET['action'];
 $phones = $_SESSION['phones'];
 $status = $_SESSION['sipstatus'];

 include("includes/db_connect.php");

 $query  = "SELECT max(id) FROM $tn";
 $result = mysql_query($query);
 $erno   = mysql_errno();
 $err    = mysql_error();
 if ($erno <> 0) die($action."|".$query."<br>".$err);
 $list   = mysql_fetch_assoc($result);
 $maxid  = $list['max(id)'];

 if ($maxid != $sipid) {
   $ct     = date('Y-m-d',time());
   $query  = "SELECT * FROM $tn WHERE ";
   $query .= " (   (event like 'Event: Newstate%') ";
   $query .= "  or (event like 'Event: Hangup%') ";
   $query .= "  or (event like 'Event: PeerStatus%') ";
   $query .= "  or (event like 'Event: Newchannel%') ";
   $query .= " ) ";
   $query .= " and (id > '$sipid') ";
   $query .= " and (timestamp > '$ct') ";
   $query .= " order by id asc ";

   $result = mysql_query($query);
   $erno = mysql_errno();
   $err  = mysql_error();
   if ($erno <> 0) die($action."|".$query."<br>".$err);

   $count     = mysql_num_rows($result);
   $numfields = mysql_num_fields($result);

   while ($list = mysql_fetch_assoc($result)) {
     $data  = trim($list['event']);
     $eflds = split(" ", $data);
     if (strtolower(substr($eflds[7],0,3)) != "sip") continue;

     $event     = $eflds[0];
     $event_val = $eflds[1];
     $pri       = $eflds[3];
     $pri_val   = $eflds[4];

     switch (strtolower($event_val)) {

/*

    Asterisk 1.8

    [0] => Event:
    [1] => PeerStatus
    [2] => 
    [3] => Privilege:
    [4] => system,all
    [5] => 
    [6] => ChannelType:
    [7] => SIP
    [8] => 
    [9] => Peer:
    [10] => SIP/earl
    [11] => 
    [12] => PeerStatus:
    [13] => Registered
    [14] => 
    [15] => Address:
    [16] => 192.168.2.3:5062


    Asterisk 1.4

    [0] => Event:
    [1] => PeerStatus
    [2] => 
    [3] => Privilege:
    [4] => system,all
    [5] => 
    [6] => Peer:
    [7] => SIP/earl
    [8] => 
    [9] => PeerStatus:
    [10] => Registered
*/

       case "peerstatus":
         if (strtolower(substr($eflds[6],0,4))      == "peer") {  $pv = $eflds[7];  $ps = $eflds[10];  }
         else if (strtolower(substr($eflds[9],0,4)) == "peer") {  $pv = $eflds[10]; $ps = $eflds[13];  }

         if (!isset($pv)) break;

         if (!in_array($pv,$phones)) $phones[] = $pv;
         if (substr($ps,0,11) == "Unreachable")  { $status[$pv] = 2; break; }
         if (substr($ps,0,12) == "Unregistered") { $status[$pv] = 2; break; }
         if (substr($ps,0,9)  == "Reachable")    { if ($status[$pv] == 1) break;  $status[$pv] = 0;  break;   }
         if (substr($ps,0,12) == "Registered")   { if ($status[$pv] == 1) break;  $status[$pv] = 0;  break;   }
         if (!isset($status[$pv])) $status[$pv] = 0;
         break;
     
       case "newchannel":
         $peer_val = split("-",$eflds[7]);
         if (!in_array($peer_val[0],$phones)) $phones[] = $peer_val[0];
         $status[$peer_val[0]] = 1;
         break;

       case "newstate":
         $peer_val = split("-",$eflds[7]);
         if (!in_array($peer_val[0],$phones)) $phones[] = $peer_val[0];
         $status[$peer_val[0]] = 1;
         break;   

       case "hangup":
         $peer_val = split("-",$eflds[7]);
         if (!in_array($peer_val[0],$phones)) $phones[] = $peer_val[0];
         $status[$peer_val[0]] = 0;
         break;
     }
   }
 }

 $action .= "|"; 
 $action .= '<table width="100%" cellpadding=2 cellspacing=2 border=0>';
 $action .= '<tr>';
 foreach ($phones as $key => $value) {
   if ( (($key %  6) == 0) && ($key != 0) ) $action .= "</tr><tr>";
   $action .= "<td align=center><button name='" . substr($value,4);
   $action .= "' onMouseOver='change_id(this,\"ButtonY\")'\n";
   $action .= "  onClick='eventlist_channel(\"" . $value . "\")'\n";
   if (isset($status[$value])) {
     if ($status[$value] == 2) {
       $action .= "  onMouseOut='change_id(this,\"ButtonU\")'\n";
       $action .= "  id='ButtonU'>\n";
     }
     else {
       if ($status[$value] == 1) {
         $action .= "  onMouseOut='change_id(this,\"ButtonR\")'\n";
         $action .= "  id='ButtonR'>\n";
       }
       else {
         $action .= "  onMouseOut='change_id(this,\"ButtonG\")'\n";
         $action .= "  id='ButtonG'>\n";
       }
     }
   }
   else {
     $action .= "  onMouseOut='change_id(this,\"ButtonB\")'\n";
     $action .= "  id='ButtonB'>\n";
   }
   $action .=  strtoupper(substr($value,4)) . "</button>\n";
   $action .= "</td>\n";
 }
 $action .= '</tr></table><br>';

 $_SESSION['sipid']     = $maxid;
 $_SESSION['phones']    = $phones;
 $_SESSION['sipstatus'] = $status;
 mysql_close($mylink);
 echo $action; 
?>
