<?php

/*
CSV functions for TinyButStrong Template Engine (for PHP 5 or higher)
Version 1.00, 2004-11-30, Condutiarii
http://www.tinybutstrong.com

Example:
	$TBS->MergeBlock('result', 'csv', 'test.csv');

Note: You can specify the CSV delimitor and length with this constant
  to define before MergeBlock method with csv source.
TBS_CSV_DELIMITER
TBS_CSV_LENGTH

*/

function tbsdb_csv_open(&$Source, &$Query)
{
  if ($handle = @fopen($Query, 'r'))
  {
    if (!defined('TBS_CSV_DELIMITER'))
      define('TBS_CSV_DELIMITER', ',');
    if (!defined('TBS_CSV_LENGTH'))
      define('TBS_CSV_LENGTH', 2048);
    $field = @fgetcsv($handle, TBS_CSV_LENGTH, TBS_CSV_DELIMITER);
    return array
    (
      'handle'     => $handle,
      'name_field' => $field
    );
  }
  else
    return false;
}

function tbsdb_csv_fetch(&$Rs, $RecNum)
{
  if ($data = @fgetcsv($Rs['handle'], TBS_CSV_LENGTH, TBS_CSV_DELIMITER))
    return array_combine($Rs['name_field'], $data) ;
  else
    return false;
}

function tbsdb_csv_close(&$Rs)
{
  @fclose($Rs);
}

?>
