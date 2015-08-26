<?php
/* Test de tbsdb_pdo.php */

include_once('./tbs_class.php');
//include_once('./tbsdb_php.php');
//include_once('./tbsdb_pdo.php');
$dbfilename = '/etc/OpenBTS/smqueue.db';
$db = new PDO('sqlite:/etc/OpenBTS/smqueue.db')  or die('Unable to open database');
$sql_ok = ( isset($db) && is_object($db) && file_exists($dbfilename) ) ? 1 : 0;

if ($sql_ok==0) $db = 'clear'; // makes the block to be cleared instead of merged with an SQL query.

$TBS = new clsTinyButStrong ;
$TBS->LoadTemplate('./smqueue-config.html') ;
$TBS->MergeBlock('blk1',$db,'select * from CONFIG') ;
$db = NULL;
$TBS->Show() ; 

?>
