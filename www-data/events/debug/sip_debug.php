<?PHP

// error_reporting(0);

 if(!isset($hn)) $hn = "localhost";
 if(!isset($un)) $un = "asteriskuser";
 if(!isset($ps)) $ps = "asterisk";
 if(!isset($tn)) $tn = "events";
 if(!isset($db)) $db = "asterisk";

 $mylink = mysql_connect($hn,$un,$ps) or die("Error: can not connect to MySQL server\n");
 mysql_select_db($db) or die("Error: select database $db failed");

 $query = "select * from $tn";
 $result = mysql_query($query);

 while($list = mysql_fetch_assoc($result)) {
   $data  = trim($list['event']);
   $event  = split(" ", $data);
   if ($event[1] == "PeerStatus") {
     print_r($event);
     echo  "\n";
   }
 }
 mysql_close($mylink);
?>
