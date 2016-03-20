<?php
/* Test de tbsdb_pdo.php */
define('TBS_CSV_DELIMITER', ',');
include_once('tbs_class.php');
include_once('plugins/tbsdb_csv_php5.php');

$csvfilename = '/home/lenovo/bin/mcc-mnc-table/mcc-mnc-table.csv';
//$sql_ok = ( isset($db) && is_object($db) && file_exists($dbfilename) ) ? 1 : 0;
$sql_ok = ( isset($csvfilename) && file_exists($csvfilename) )  ? 1 : 0;
//if ($sql_ok==0) $db = 'clear'; // makes the block to be cleared instead of merged with an SQL query.
$TBS = new clsTinyButStrong ;
$TBS->LoadTemplate('mcc-mnc.html') ;
$TBS->MergeBlock('blk1', 'csv', $csvfilename);
$TBS->Show() ; 

?>
