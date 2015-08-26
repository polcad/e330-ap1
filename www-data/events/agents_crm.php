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

 include("includes/header.php");
 $cid = $_GET['id'];

 echo "<body>";
 echo "<center>";
 echo "<br><br>";
 echo "<H1>Caller ID: $cid </H1>\n";
 echo "<br><br>";
 echo "<br><br>";
 echo "<br><br>";
 echo "This is the customizable Agent CRM screen.<br>";
 echo "</center>\n</body>\n</html>\n";
 exit();
?>
