<?php
/* Dictador 2015 */

include_once('./plugins/tbs_plugin_mergeonfly.php');
include_once('./tbs_class.php');
//include_once('./plugins/tbs_plugin_bypage.php');
//include_once('./plugins/tbs_plugin_navbar.php');

$page_num  = isset($_GET['page_num']) ? $_GET['page_num'] : 1;
$page_size = 20;
$rec_cnt   = isset($_GET['rec_cnt'])  ? $_GET['rec_cnt']  : -1;

$dbfilename = '/var/lib/asterisk/sqlite3dir/sqlite3.db';
$db = new PDO('sqlite:/var/lib/asterisk/sqlite3dir/sqlite3.db')  or die('Unable to open database');
$sql_ok = ( isset($db) && is_object($db) && file_exists($dbfilename) ) ? 1 : 0;

if ($sql_ok==0) $db = 'clear'; // makes the block to be cleared instead of merged with an SQL query.

$TBS = new clsTinyButStrong ;
$TBS->LoadTemplate('./subscriber-registry.html') ;
// Merge the block by page
//$TBS->PlugIn(TBS_BYPAGE,$page_size,$page_num,$rec_cnt);
//$TBS->PlugIn(TBS_ONFLY, 100); // tbs_plugin_mergeonfly.php plugin causes rec_cnt to fail
$rec_cnt = $TBS->MergeBlock('blk1',$db,'select * from SIP_BUDDIES');
//$TBS->MergeBlock('blk1',$db,'select * from SIP_BUDDIES') ;
//$TBS->PlugIn(TBS_BYPAGE,$page_size,$page_num,$rec_cnt);
$TBS->PlugIn(TBS_ONFLY, 100);
$TBS->MergeBlock('blk2',$db,'select * from DIALDATA_TABLE');
//$TBS->PlugIn(TBS_BYPAGE,$page_size,$page_num,$rec_cnt);
$TBS->MergeBlock('blk3',$db,'select * from RRLP') ;
//$TBS->PlugIn(TBS_BYPAGE,$page_size,$page_num,$rec_cnt);
$TBS->MergeBlock('blk4',$db,'select * from rates') ;
// Merge the Navigation Bar
//$TBS->PlugIn(TBS_NAVBAR,'nv','',$page_num,$rec_cnt,$page_size);
//$TBS->Show(TBS_NOTHING);

$db = NULL;
$TBS->Show() ; 

?>
