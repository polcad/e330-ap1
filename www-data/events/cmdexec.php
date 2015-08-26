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
if (!isset($_SESSION['Login'])) exit();

include('includes/header.php');
echo "<body>";
echo "<H1>Asterisk Manager Command Execution</H1>";
echo '<pre>';
$logoff   = "Action: Logoff\r\n\r\n";
$action   = "";
$parms    = "";

if (isset($_GET['action']))  $action =  $_GET['action'];
if (isset($_POST['action'])) $action = $_POST['action'];
if (isset($_GET['parms']))   $parms  =  $_GET['parms'];
if (isset($_POST['parms']))  $parms  = $_POST['parms'];

include('includes/manager_login.php');

echo "ASTERISK MANAGER INPUT\n";
echo $action."\n";
echo $parms."\n\n";

if ($parms != "") {
  $action  = $action . "\r\n";
  $parms = $parms . "\r\n\r\n";
}
else $action = $action . "\r\n\r\n";

fputs($socket, $action);
if ($parms != "") fputs($socket,$parms);
fputs($socket, $logoff);

echo "ASTERISK MANAGER OUTPUT\n";
flush();
while (!feof($socket)) { echo fread($socket, 2048);  flush(); }
fclose($socket);
echo '</pre>';
echo "<center>";
include('includes/functions.php');
buttons("");
echo "</center>";
echo "</body></html>";
?>
