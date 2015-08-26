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
 if (!isset($_SESSION['Login'])) exit();

 $eventid = $_SESSION['eventid'];
 $action  = $_GET['action'];

 include("includes/db_connect.php");

 $query  = "SELECT max(id) FROM $tn";
 $result = mysql_query($query);
 $erno = mysql_errno();
 $err  = mysql_error();
 if ($erno <> 0) die($action."|".$query."<br>".$err);
 $list  = mysql_fetch_assoc($result);
 $maxid = $list['max(id)'];
 if ($maxid == $eventid) { echo $action; mysql_close($mylink); exit(); }

 $query = "SELECT * FROM $tn order by id desc limit 20";

 $result = mysql_query($query);
 $erno = mysql_errno();
 $err  = mysql_error();
 if ($erno <> 0) die($action."|".$query."<br>".$err);

 $count = mysql_num_rows($result);
 $numfields = mysql_num_fields($result);
 $rows = 0;

 $action .= "|"; 

 $action .= '<table width="100%" cellpadding=2 cellspacing=0 border=1>';
 $action .= "<tr><th>ID</th><th>TIME</th><th>EVENT</th>";
 $action .= "<th>CHANNEL / PEER / DATA</th>";
 $action .= "<th>STATUS / DATA</th><th>DATA</th><th>PRIVILEGE</th></tr>";

// $tdnowrap  = "<td nowrap onMouseover=\"this.style.backgroundColor='yellow'\"";
// $tdnowrap .= " onMouseout=\"this.style.backgroundColor='white'\">";

 $tdnowrap  = "<td nowrap>";
 $tdblank   = "<td nowrap>&nbsp;</td>";

 while ($rows < $count) {
   $list      = mysql_fetch_assoc($result);
   $id        = $list['id'];
   $timestamp = $list['timestamp'];
   $event     = $list['event'];
   $flds      = split("  ",$event);
   $c         = count($flds);
   if  (($rows%2) == 0) $action .= "<tr>";
   else                 $action .= "<tr bgcolor=#D3D3D3>";

   if  (strpos($event, "Newchannel") !== false) $action .= "<tr bgcolor=#03D353>"; // dictador  
   if  (strpos($event, "Hangup") !== false) $action .= "<tr bgcolor=#E55350>"; // dictador
   
   $action .= $tdnowrap . "<a href=eventlist.php?rec=$id>$id</a></td>";
   $action .= $tdnowrap . substr($timestamp,-8) . "</td>";
  
   if (substr($flds[0],0,5) == "Event") { 
     $action .= $tdnowrap . substr($flds[0],7) . "</td>";
     for($i=2;$i<4;++$i) {
       if (isset($flds[$i])) {
         if ($flds[$i] != "") $action .= $tdnowrap . htmlentities($flds[$i]) . "</td>";
         else                 $action .= $tdnowrap . "&nbsp;</td>";
       }
       else                   $action .= $tdnowrap . "&nbsp;</td>";
     }
     if ($c > 4) {
       $action .= $tdnowrap;
       for($i=4;$i<$c;++$i) { 
         if($flds[$i] != "") $action .= htmlentities($flds[$i]) . "  ";
         else                $action .= "&nbsp; ";
       }
       $action .= "</td>";
     }
     else  $action .= $tdblank;
     if ($flds[1] != "" ) $action .= $tdnowrap . htmlentities($flds[1]) . "</td>";
     else                 $action .= $tdblank;
   }
   else  {
     $action .= $tdblank.$tdblank;
     $action .= $tdnowrap . htmlentities($event) . "</td>";
     $action .= $tdblank.$tdblank;
   }

   $action .= '</tr>';
   if ($id > $eventid) $eventid = $id;
   $rows++;
 }

 $action .= '</table>';
 $_SESSION['eventid'] = $eventid;
 mysql_close($mylink);
 echo $action;
?>
