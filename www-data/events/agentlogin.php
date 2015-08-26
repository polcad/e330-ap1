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

$agent = $_GET['agent'];

include('includes/manager_login.php');
fputs($socket, "Action: AgentCallBackLogin\r\n");
fputs($socket, "Agent: " . $agent . "\r\n");

//
//  This needs modified for specific agent IDs as it is installation specific
//

fputs($socket, "Exten: " . $agent . "\r\n");
fputs($socket, "Context: call_center\r\n\r\n");

//
// End of specific changes

fputs($socket, "Action: Logoff\r\n\r\n");

$data = "";
while (!feof($socket)) $data .= fread($socket, 4096);

fclose($socket);

header("Location: queues.php");
exit();
?>
