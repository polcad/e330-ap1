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

$general     = 0;
$enabled     = 0;
$errno       = "";
$errstr      = "";
$timeout     = "30";

/*
   Change the variables below for your
   Asterisk system if you are not using
   /etc/asterisk/manager.conf

*/

$astmanager  = "";
$astpassword = "";
$astip       = "127.0.0.1";
$astport     = "5038";

/*
-------------- START DELETE --------------------

Delete this section if you need to connect to
an Asterisk Server that is different than
the web server or you don't want to use
the /etc/asterisk/manager.conf file

If you delete this section, make sure to

update the above variables to correct values

$astmanager   is the  Manager User ID
$astpassword  is the secret (password)
$astip        is the IP address of the Asterisk Server
$astpor       is the manager port


*/

$fname = "/etc/asterisk/manager.conf";

$fd = fopen ($fname, "r");
if (!$fd) {
 echo "An error occurred determining the Asterisk Manager Configuration!<br>"; 
 echo "The PHP/WEB interface could not open the file -> $fname <br>"; 
 exit(0);
}

while (!feof ($fd)) {
  $buffer = fgets($fd, 4096);
  $l = trim($buffer);
  $pattern = '/^\s*\[(.*?)\]/';
  if (preg_match($pattern,$l,$matches)) {
    if ($matches[0] == "general") {
      $general = 1;
    }
    else {
       $astmanager = $matches[1];
    }
    continue;
  }
  $pattern  = '/^\s*(\w+)\s*=>\s*(.+)\s*;?.*$/';
  if (preg_match($pattern,$l,$matches)) { 
      if (strtolower($matches[0]) == "enabled = yes") {
        $enabled = 1;
        continue;
      }
      if (substr(strtolower($matches[0]),0,9) == "secret = ") {
        $astpassword = $matches[2];
        continue;
      }
      if (substr(strtolower($matches[0]),0,7) == "port = ") {
        $astport = $matches[2];
        continue;
      }
  }
  $pattern  = '/^\s*(\w+)\s*=\s*(.+)\s*;?.*$/';
  if (preg_match($pattern,$l,$matches)) { 
      if (strtolower($matches[0]) == "enabled = yes") {
        $enabled = 1;
        continue;
      }
      if (substr(strtolower($matches[0]),0,9) == "secret = ") {
        $astpassword = $matches[2];
        continue;
      }
      if (substr(strtolower($matches[0]),0,7) == "port = ") {
        $astport = $matches[2];
        continue;
      }
  }
}
fclose ($fd); 

if ($enabled == 0) {
 echo "The Asterisk Manager Configuration is not enabled!"; 
 echo "<br>Please check the file -> $fname to enable it!"; 
 echo "<br>Enabled  : " . $enabled;
 echo "<br>Manager  : " . $astmanager;
 echo "<br>Password : " . $astpassword;
 echo "<br>IP       : " . $astip;
 echo "<br>Port     : " . $astport;
 exit(0);
}

/*

-------------------END DELETE----------------------

*/


$socket = fsockopen($astip,$astport,$errno,$errstr,$timeout);
if (!$socket) {
    echo "<pre>";
    echo "$errstr ($errno)<br>\n";
    echo "</pre>";
    exit(99);
}

fputs($socket, "Action: Login\r\n");
fputs($socket, "UserName: $astmanager\r\n");
fputs($socket, "Secret: $astpassword\r\n\r\n");

?>
