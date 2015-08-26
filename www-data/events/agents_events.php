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

 $aeventid = $_SESSION['aeventid'];
 $action  = $_GET['action'];

 include("includes/db_connect.php");

 $query  = "SELECT max(id) FROM $tn";
 $result = mysql_query($query);
 $erno = mysql_errno();
 $err  = mysql_error();
 if ($erno <> 0) die($action."|".$query."<br>".$err);
 $list  = mysql_fetch_assoc($result);
 $maxid = $list['max(id)'];

 if ($maxid == $aeventid) { echo $action; mysql_close($mylink); exit(); }

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
   $event     = $list['event'];
   if ($id > $aeventid) $aeventid = $id;
   $rows++;
   $flds      = split("  ",$event);

   if  (($rows%2) == 0) $action .= "<tr>";
   else                 $action .= "<tr bgcolor=#D3D3D3>";

   $action .= $tdnowrap . "<a href=eventlist.php?rec=$id>$id</a></td>";
   $timestamp = $list['timestamp'];
   $action .= $tdnowrap . substr($timestamp,-8) . "</td>";

   if (substr($flds[0],0,5) != "Event") {
     $action .= $tdblank.$tdblank.$tdblank;
     $action .= $tdnowrap . $event . "</td>";
     $action .= '</tr>';
     continue; 
   }

   $type = trim(substr($flds[0],7));
   if (strtolower(substr($type,0,5)) == "agent") {
     $action .= "$tdnowrap<a href=\"javascript:open_new_window($id)\">$type</a></td>";
   }
   else {
     if (strtolower(substr($type,0,10)) == "Newchannel") {
       $action .= "<script language='JavaScript'>\n<!--\nopen_new_window($id);\n// -->\n";
       $action .= "</script>\n";
     }
     else $action .= $tdnowrap . $type . "</td>";
   }

   for($i=2;$i<4;++$i) $action .= $tdnowrap . "$flds[$i]</td>";
   $c = count($flds);
   if ($c > 4) {
     $action .= $tdnowrap;
     for($i=4;$i<$c;++$i) $action .= $flds[$i] . "  ";
     $action .= "</td>";
   }
   else  $action .= $tdblank;
   $action .= $tdnowrap . "$flds[1]</td>";

   $action .= '</tr>';
 }

 $action .= '</table>';
 $_SESSION['aeventid'] = $aeventid;
 mysql_close($mylink);
 echo $action;
?>
