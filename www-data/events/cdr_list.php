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
 include("includes/functions.php");
 
//Fields of the CDR in Asterisk
//-----------------------------
//
//   1. accountcode: What account number to use, (string, 20 characters)
//   2. src: Caller*ID number (string, 80 characters)
//   3. dst: Destination extension (string, 80 characters)
//   4. dcontext: Destination context (string, 80 characters)
//   5. clid: Caller*ID with text (80 characters)
//   6. channel: Channel used (80 characters)
//   7. dstchannel: Destination channel if appropriate (80 characters)
//   8. lastapp: Last application if appropriate (80 characters)
//   9. lastdata: Last application data (arguments) (80 characters)
//  10. start: Start of call (date/time)
//  11. answer: Answer of call (date/time)
//  12. end: End of call (date/time)
//  13. duration: Total time in system, in seconds (integer), from dial to hangup
//  14. billsec: Total time call is up, in seconds (integer), from answer to hangup
//  15. disposition: What happened to the call: ANSWERED, NO ANSWER, BUSY
//  16. amaflags: What flags to use: DOCUMENTATION, BILL, IGNORE etc, 
//      specified on a per channel basis like accountcode.
//  17. user field: A user-defined field, maximum 255 characters 

$pattern = "";
if (isset($_GET['pattern']))  $pattern = $_GET['pattern'];
if (isset($_POST['pattern'])) $pattern = $_POST['pattern'];

echo "<body><center>";

if ($pattern == "") {
  echo "<H1><b>CDR RECORDS [Master.csv] LIST</b></H1>";
  echo "<br><br>";
  echo "<H2>No Search Pattern Specified!</H2>"; 
  buttons("");
  echo "</center></body></html>";
  exit(0);
}

echo "<H1><b>";
echo "CDR RECORDS [Master.csv] LIST";
echo "<br>Records Containing Search String: $pattern";
echo "</b></H1>";

$fname = "/var/log/asterisk/cdr-csv/Master.csv";
$fd = fopen ($fname, "r");
if (!$fd) {
 echo "<br><br>";
 echo "Error opening $fname"; 
 echo "</body></html>";
 exit(0);
}
echo '<table width="100%" cellpadding=2 cellspacing=0 border=1>';
echo "<tr>";
echo "<th>Account</th>";
echo "<th>Src</th>";
echo "<th>Dst</th>";
echo "<th>Dst Context</th>";
echo "<th>Caller ID</th>";
echo "<th>Channel</th>";
echo "<th>Dst Channel</th>";
echo "<th>Last App</th>";
echo "<th>Last Data</th>";
echo "<th>Start</th>";
echo "<th>Answer</th>";
echo "<th>End</th>";
echo "<th>Duration</th>";
echo "<th>Bill Secs</th>";
echo "<th>Disposition</th>";
echo "<th>AMA flags</th>";
echo "<th>User Field</th>";
echo "</tr><tr></tr>";
$d = 0;
$recs = 0;
while (!feof ($fd)) {
  $buffer = fgets($fd, 4096);
  $l = trim($buffer);
  if ($pattern != "") {
    if (!strstr($l,$pattern)) continue;
  }
  $recs += 1;
  $badcommapat = '/\"[^\"]+,[^\",]+\"/';
  if (preg_match($badcommapat,$l,$matches)) {
      $fixcomma = str_replace(",","-",$matches[0]);
      $l = str_replace($matches[0],$fixcomma,$l);
  }
  $e = explode(",",$l);
  $len = sizeof($e);
  if  (($recs%2) == 0) echo "<tr>";
  else                 echo "<tr bgcolor=#D3D3D3>";
  for ($c=0;$c<$len;++$c) {
    echo $tdnowrap;
    $e[$c] = trim($e[$c],"\r\n \"");
    if ($c == 4) $e[$c] = str_replace ("\"", "", $e[$c]);
    if ($e[$c] == "") echo "&nbsp;";
    else echo htmlspecialchars($e[$c]);
    echo "</td>";
  }
  while ($c < 17) { echo "<td>&nbsp;</td>"; ++$c; }
  echo "</tr>\n";
  flush();
  ++$d;
}
echo "</table>";
echo "<br><br>";
echo "Total Record Count: $recs";
echo "<br>";
buttons("");

fclose ($fd); 
echo "</center></body></html>";
?>
