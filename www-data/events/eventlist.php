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
 session_start();
 if (!isset($_SESSION['Login'])) header("Location: login.php");

 include("includes/header.php");
 include("includes/db_connect.php");
 include("includes/functions.php");

 echo "<body>";
 echo "<center>";
 $pattern = "";
 $search = "";
 $chan   = "";
 $agent  = "";
 $rec    = "";
 $limit  = "";

 if (isset( $_GET['search'])) $search =  $_GET['search'];
 if (isset($_POST['search'])) $search = $_POST['search'];

 if (isset($_GET['agent'])) $agent = $_GET['agent'];
 if (isset($_GET['chan']))  $chan = $_GET['chan'];
 if (isset($_GET['rec']))   $rec  = $_GET['rec'];

 if ($chan  != "") { $search = $chan;  $limit  = 20; }
 if ($agent != "") { $search = $agent; $limit  = 20; }

 if ($rec != "") {
   $query  = "SELECT * FROM $tn WHERE id='" . $rec . "'";
   $result = mysql_query($query);
   $erno = mysql_errno();
   $err  = mysql_error();
   if ($erno <> 0) die($query."<br>".$err);
   $list = mysql_fetch_assoc($result);
   echo "<H1>ASTERISK EVENT LOG LIST</H1>\n";
   $data  = trim($list['event']);
   $flds  = split("  ",$data);
   $c     = count($flds);
   echo "</center>";
   echo "<table cellpadding=2 cellspacing=0 border=0>\n";
   echo "<tr><td>TimeStamp: ";
   echo $list['timestamp'];
   echo "</td></tr>\n";
   for ($i=0;$i<$c;++$i) {
     $val = trim($flds[$i]); 
     echo "<tr>\n<td>";
     echo htmlentities($val);
     echo "</td>\n";
//   if (substr($val,0,7) == "Channel") $pattern = substr($val,9); 
     if ( (substr($val,0,9) == "CallerID1") ||
          (substr($val,0,9) == "CallerID2") ||
          (substr($val,0,9) == "CallerID:") ||
          (substr($val,0,9) == "Extension") ) {
       if (substr($val,0,9) == "CallerID1") $pattern = substr($val,11); 
       if (substr($val,0,9) == "CallerID2") $pattern = substr($val,11); 
       if (substr($val,0,9) == "CallerID:") $pattern = substr($val,10); 
       if (substr($val,0,9) == "Extension") $pattern = substr($val,11); 
       echo "<td nowrap><button id=Sel name='CDR RECORD' ";
       echo " onMouseOver='change_id(this,\"ButtonY\")' ";
       echo " onMouseOut='change_id(this,\"Sel\")' ";
       echo "onClick='location.href=\"cdr_list.php?pattern=$pattern\"";
       echo "'>CDR RECORD</button></td>\n";
     }
     else echo "<td>&nbsp;</td>\n";
     echo "</tr>\n";
   }
   echo "</table>\n";
   echo "<center>\n";
   echo "<form name=fform>\n";
   echo "<input type=\"hidden\" name=\"command\"  value=\"\">\n";
   $channel = "";
   for ($i=0;$i<$c;++$i) {
     $val = trim($flds[$i]); 
     if (substr($val,0,7) == "Channel") $channel = substr($flds[$i],9);
   }
   echo "<input type=\"hidden\" name=\"channel\" value=\"" . htmlentities($channel) . "\">\n";
   echo "</form>\n";
   commands_buttons();
 }
 else {
   if (isset($_POST['sdate'])) {
     $sdate = $_POST['sdate'];
     $edate = $_POST['edate'];
     $query = "SELECT * FROM $tn where (timestamp >= '" . $sdate . "')";
     $query = $query . " and (timestamp <= '" . $edate . "') order by id";
   }
   else {
     if ($search == "") {
       echo "<H1>ASTERISK EVENT LOG LIST</H1>\n";
       echo "<br>\n";
       echo "<H2>No Search Pattern Specified!</H2>\n";
       echo "<form action=" . $_SERVER['PHP_SELF'] . " method=post>";
       $sdate = date('Y-m-d H:i:s',time());
       $edate = date('Y-m-d H:i:s',time());
       echo "<input type=text name=sdate size=20 value='" . $sdate;
       echo "'>&nbsp;Start Date/Time<br>";
       echo "<input type=text name=edate size=20 value='" . $edate;
       echo "'>&nbsp;&nbsp;End Date/Time<br>";
       echo "<br><br>";
       echo "<input type=submit>";
       echo "&nbsp;&nbsp;";
       echo "<input type=reset>";
       echo "</form>";
       echo "<br><br>";
       buttons($pattern);
       echo "</center>\n</body>\n</html>\n";
       mysql_close($mylink);
       exit();
     } 
     $ct = date('Y-m-d',time());
     $query  = "SELECT * FROM $tn WHERE ";
     if (substr($search,0,2) == "=>") $query .= substr($search,2);
     else {
       $query .= " (event like '%" . $search . "%') ";
       $query .= " and (timestamp > '" . $ct . "') order by id desc";
       if ($limit != "") $query .= " limit " . $limit;
     }
   } 

   $result = mysql_query($query);
   $erno = mysql_errno();
   $err  = mysql_error();
   if ($erno <> 0) die($query."<br>".$err);
 
   $count = mysql_num_rows($result);

   $numfields = mysql_num_fields($result);
   $rows = 0;

   echo "<H1>ASTERISK EVENT LOG LIST</H1>\n";
   echo "<H2>$query</H2>\n";
  
   echo "<table width=\"100%\" cellpadding=2 cellspacing=0 border=1>\n";
   echo "<tr><th>ID</th><th>TIME STAMP</th><th>EVENT</th>\n";
   echo "<th>PRIVILEGE</th><th>CHANNEL / PEER / DATA</th><th>STATUS / DATA</th>\n";
   echo "<th>DATA</th></tr>\n";

   while ($rows < $count) {
     $list      = mysql_fetch_assoc($result);
     $id        = $list['id'];
     $timestamp = $list['timestamp'];
     $event     = $list['event'];
     $flds      = split("  ",$event);
     $c         = count($flds);

     if  (($rows%2) == 0) echo "<tr>";
     else                 echo "<tr bgcolor=#D3D3D3>";

     echo $tdnowrap . "<a href=eventlist.php?rec=$id>$id</a></td>";
     echo $tdnowrap . "$timestamp</td>";

     for($i=0;$i<4;++$i) {
       if (isset($flds[$i])) {
         if ($flds[$i] != "")  echo $tdnowrap . htmlentities($flds[$i]) . "</td>"; 
         else                  echo $tdnowrap . "&nbsp;</td>"; 
       }
       else                    echo $tdnowrap . "&nbsp;</td>";
     }
     if ($c > 4) {
       echo $tdnowrap;
       for($i=4;$i<$c;++$i) {
         if ($flds[$i] != "") echo htmlentities($flds[$i]) . "  ";
         else                 echo "&nbsp; ";
       }
       echo "</td>";
     }
     else  echo $tdnowrap . "&nbsp</td>";
     echo "</tr>";
     $rows++;
   }

   echo "</table>\n";
 }
 buttons($pattern);

 echo "</center>\n</body>\n</html>\n";
 mysql_close($mylink);
 exit();
?>
