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

 $taskid = $_SESSION['taskid'];
 $action = "";

 include("includes/db_connect.php");

 if ($taskid == 0) {
   $query  = "SELECT max(id) FROM $tn";
   $result = mysql_query($query);
   $erno = mysql_errno();
   $err  = mysql_error();
   if ($erno <> 0) die($query."<br>".$err);
   $list  = mysql_fetch_assoc($result);
   $maxid = $list['max(id)'];
   $_SESSION['taskid'] = $maxid;
   mysql_close($mylink);
   echo $action;
   exit();
 }

 $query = "SELECT * FROM $tn where (id > $taskid) order by id";

 $result = mysql_query($query);
 $erno = mysql_errno();
 $err  = mysql_error();
 if ($erno <> 0) die("|".$query."<br>".$err);

 $count = mysql_num_rows($result);

 $numfields = mysql_num_fields($result);
 $rows = 0;

 while ($rows < $count) {
   $list      = mysql_fetch_assoc($result);
   $id        = $list['id'];
   $event     = $list['event'];
   $rows++;
   $taskid = $id;
   $flds      = split("  ",$event);
   if (substr($flds[0],0,5) != "Event") continue; 
   $type = trim(substr($flds[0],7));

   if (strtolower(substr($type,0,4)) == "link") {
     $data  = trim($event);
     list($event,$evv,$eve,$pv,$pvv,$pve,$c1,$c1v,$c1e,$c2,$c2v,$c2e,$u1,$u1v,$u1e,$u2,$u2v,$u2e,$cid1,$cid1v,$cid1e,$cid2,$cid2v,$cid2e,$extra) = split(" ", $data, 25);
     $action .= "|" . $cid1v;
     break;
   }
 }

 $_SESSION['taskid'] = $taskid;
 mysql_close($mylink);
 echo $action;
 exit();
?>
