<?php
/*
 Copyright (C) 2006 Earl C. Terwilliger
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

//
//  $hn  host name
//  $un  user name
//  $ps  password
//  $tn  table name
//  $db  data base
//

 if(!isset($hn)) $hn = "localhost";
 if(!isset($un)) $un = "asteriskuser";
 if(!isset($ps)) $ps = "asterisk";
 if(!isset($tn)) $tn = "events";
 if(!isset($db)) $db = "asterisk_events";

 $mylink = mysql_connect($hn,$un,$ps) or die("Error: can not connect to MySQL server\n");
 mysql_select_db($db) or die("Error: select database $db failed");

?>
