<?php
/*
ADO functions for TinyButStrong Template Engine
Version 1.00, 2006-07-28, golivier
Version 1.01, 2006-10-19, Skrol29 - fix a bug about error message on tbsdb_pdo_open() "Call to a member function errorInfo()"
http://www.tinybutstrong.com

Example:
  $dbh = new PDO('mysql:host=localhost;dbname=test', $user, $pass); for mysql
  $dbh = new PDO('sqlite:/path/to/database'); for sqlite
  $dbh = new PDO('firebird:User=john;Password=mypass;Database=DATABASE.fdb;DataSource=localhost;Port=3050'); for sqlite
  ...
  
  $TBS->MergeBlock('blk1,$dbh,'SELECT * FROM t_examples');
*/

function tbsdb_pdo_open(&$Source,&$Query) {
	
	$Rs = $Source->query($Query);
	
	$errI = $Source->errorInfo();
	if (isset($errI[2])) {
    	echo 'PDO DB Error: "<font color="#FF0000">'.$errI[2].'</font>" on query "<font color="#FF0000">'.$Query.'</font>"<br>';
		return false;
	} else {
		return $Rs;
	}
}

function tbsdb_pdo_fetch(&$Rs) {
	$Row = $Rs->fetch(1); //PDO_FETCH_ASSOC
	if (is_null($Row)) 
	  $Row = false;
 
	return $Row;
}

function tbsdb_pdo_close(&$Rs) {
	$Rs->closeCursor();
}

?>