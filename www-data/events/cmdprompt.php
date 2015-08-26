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

if (isset($_POST['submit'])) {
  $hdr  = "Location: cmdexec.php?action=";
  $hdr .= htmlentities("Action: " . $_POST['action']);
  $hdr .= "&parms=";
  while (list($name, $value) = each($_POST)) {
    if ($name  == "submit") continue;
    if ($name  == "action") continue;
    if ($value == "")       continue;
    $hdr .= htmlentities($name . ": ");
    $hdr .= htmlentities($value . "%0d%0a");
  }
  header($hdr);
  exit();
}
 include("includes/header.php");
 include("includes/functions.php");

 echo "<body>";
 echo "<center>";

 $action =  $_GET['action'];
 if ($action == 'transfer') $action = "redirect";
 echo "<H1>ASTERISK MANAGER COMMAND EXECUTION</H1>";
 echo "<form action=" . $_SERVER['PHP_SELF'] . " method=post>";
 echo '<table cellpadding=2 cellsizing=2 border=0>';
 echo "<tr>";
 echo "<td nowrap>ACTION:&nbsp;</td>";
 echo "<td nowrap>";
 echo "<input type=text name=action size=40 value='" . $action  . "'>";
 echo "</td></tr>";
 if (isset($_GET['prompt'])) {
   $e = explode("|",$_GET['prompt']);
   $ln = count($e);
   for ($c=0;$c<$ln;++$c) {
     echo "<tr>";
     echo "<td nowrap>" . strtoupper($e[$c]) . ":&nbsp;</td>";
     echo "<td>" . "<input type=text name=\"" . $e[$c] . "\" size=40>" . "</td>";
     echo "</tr>";
   }
 }
 else {
   while (list($name, $value) = each($_GET)) {
     if ($name == 'action') continue;
     echo "<td nowrap>";
     echo strtoupper($name) . " :";
     echo "</td><td nowrap>";
     echo "<input type=text name='" . $name . "' size=40 value='" . $value . "'>";
     echo "</td></tr>";
   }
   if ($action == 'monitor') {
     echo "<tr>";
     echo "<td nowrap>Channel:&nbsp;</td>";
     echo "<td><input type=text name=channel size=40></td>";
     echo "</tr>";
     echo "<tr>";
     echo "<td nowrap>File:&nbsp;</td>";
     echo "<td><input type=text name=file size=40 value='";
     echo date("Y-m-d-H:i:s") . "-channel";
     echo "'></td>";
     echo "</tr>";
     echo "<tr>";
     echo "<td nowrap>Mix:&nbsp;</td>";
     echo "<td><input type=text name=mix  size=40 value=1></td>";
     echo "</tr>";
   }
   if ($action == 'hangup') {
     if (!isset($_GET['channel'])) {
       echo "<tr>";
       echo "<td nowrap>Channel:&nbsp;</td>";
       echo "<td><input type=text name=channel size=40></td>";
       echo "</tr>";
     }
   } 
   if ($action == 'zaptransfer') {
     echo "<tr>";
     echo "<td nowrap>ZapChannel:&nbsp;</td>";
     echo "<td><input type=text name=zapchannel size=40></td>";
     echo "</tr>";
   }
   if ($action == 'redirect') { 
     echo "<tr>";
     echo "<td nowrap>Channel:&nbsp;</td>";
     echo "<td><input type=text name=channel size=40></td>";
     echo "</tr>";
     echo "<tr>";
     echo "<td nowrap>ExtraChannel:&nbsp;</td>";
     echo "<td><input type=text name=extrachannel size=40></td>";
     echo "</tr>";
     echo "<tr>";
     echo "<td nowrap>Exten:&nbsp;</td>";
     echo "<td><input type=text name=exten size=40></td>";
     echo "</tr>";
     echo "<tr>";
     echo "<td nowrap>Context:&nbsp;</td>";
     echo "<td><input type=text name=context size=40></td>";
     echo "</tr>";
     echo "<tr>";
     echo "<td nowrap>Priority:&nbsp;</td>";
     echo "<td><input type=text name=priority size=40 value=1></td>";
     echo "</tr>";
   }
 }
 echo "</table>";
 echo "<br><br>";
 echo "<input id=Sel ";
 echo " onMouseOver='change_id(this,\"ButtonY\")' ";
 echo " onMouseOut='change_id(this,\"Sel\")' ";
 echo " type=submit name=submit value=' EXECUTE '>";
 echo "&nbsp;&nbsp;&nbsp;&nbsp;";
 echo "<input id=Sel ";
 echo " onMouseOver='change_id(this,\"ButtonY\")' ";
 echo " onMouseOut='change_id(this,\"Sel\")' ";
 echo " type=reset value=' RESET '>";
 echo "</form>";

 buttons($pattern);

 echo "</center></body></html>";
 exit();

?>
