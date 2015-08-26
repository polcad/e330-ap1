<?php
/* Dictador 2015 */

include_once('./tbs_class.php');

$dbfilename = '/etc/OpenBTS/OpenBTS.db';
$db = new PDO('sqlite:/etc/OpenBTS/OpenBTS.db')  or die('Unable to open database');
$sql_ok = ( isset($db) && is_object($db) && file_exists($dbfilename) ) ? 1 : 0;

if ($sql_ok==0) $db = 'clear'; // makes the block to be cleared instead of merged with an SQL query.

$TBS = new clsTinyButStrong ;
$TBS->LoadTemplate('./openbts-config.html') ;
$TBS->MergeBlock('blk1',$db,'select * from CONFIG where KEYSTRING like \'%\' ORDER BY KEYSTRING') ;
$db = NULL;
$TBS->Show() ; 

?>
