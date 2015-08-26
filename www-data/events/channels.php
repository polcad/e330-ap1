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
 if (!isset ($_SESSION)) session_start();
 if (!isset($_SESSION['channelid'])) exit();

 $channelid = $_SESSION['channelid'];
 $action    = $_GET['action'];
 $channels  = $_SESSION['channels'];
 $status    = $_SESSION['chanstatus'];

 include("includes/db_connect.php");

 $query  = "SELECT max(id) FROM $tn";
 $result = mysql_query($query);
 $erno = mysql_errno();
 $err  = mysql_error();
 if ($erno <> 0) die($action."|".$query."<br>".$err);
 $list  = mysql_fetch_assoc($result);
 $maxid = $list['max(id)'];

 if ($maxid != $channelid) {
   $ct = date('Y-m-d',time());
   $query  = "SELECT * FROM $tn WHERE ";
   $query .= " (   (event like 'Event: Newstate%') ";
   $query .= "  or (event like 'Event: Newchannel%') ";
   $query .= "  or (event like 'Event: Hangup%') ";
   $query .= " ) ";
   $query .= " and (id > '$channelid') ";
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
     list($event,$event_val,$ev,$priv,$priv_val,$pv,$chan,$chan_val,$cv,$extra) = split(" ", $data, 10);
     if (strtolower(substr($chan_val,0,3)) == "zap") { 
       if (!in_array($chan_val,$channels)) $channels[] = $chan_val;
       if ($event_val == "Hangup")     $status[$chan_val] = 0;
       if ($event_val == "Newstate")   $status[$chan_val] = 1;
       if ($event_val == "Newchannel") $status[$chan_val] = 1;
     }
     if (strtolower(substr($chan_val,0,5)) == "dahdi") { 
       if (!in_array($chan_val,$channels)) $channels[] = $chan_val;
       if ($event_val == "Hangup")     $status[$chan_val] = 0;
       if ($event_val == "Newstate")   $status[$chan_val] = 1;
       if ($event_val == "Newchannel") $status[$chan_val] = 1;
     }
   }
 }

 $action .= "|"; 
 $action .= '<table width="100%" cellpadding=2 cellspacing=2 border=0>';
 $action .= '<tr>';
 $c = count($channels);
 for ($i=0;$i<$c;++$i)  {
   if ( (($i %  6) == 0) && ($i != 0) ) $action .= "</tr><tr>";
   $action .= "<td align=center><button name='s" . $channels[$i];
   $action .= "' onMouseOver='change_id(this,\"ButtonY\")'\n";
   $action .= "  onClick='eventlist_channel(\"" . $channels[$i] . "\")'\n";
   if (isset($status[$channels[$i]])) {
     if ($status[$channels[$i]] == 1) { 
       $action .= "  onMouseOut='change_id(this,\"ButtonR\")'\n";
       $action .= "  id='ButtonR'>\n";
     }
     else {
       $action .= "  onMouseOut='change_id(this,\"ButtonG\")'\n";
       $action .= "  id='ButtonG'>\n";
     }
   }
   else {
     $action .= "  onMouseOut='change_id(this,\"ButtonB\")'\n";
     $action .= "  id='ButtonB'>\n";
   }
   $action .=  strtoupper($channels[$i]) . "</button>\n";
   $action .= "</td>\n";
 }
 $action .= '</tr></table><br>';

 $_SESSION['channelid']  = $maxid;
 $_SESSION['channels']   = $channels;
 $_SESSION['chanstatus'] = $status;
 mysql_close($mylink);
 echo $action; 
?>
