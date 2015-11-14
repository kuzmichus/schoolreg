<?php
/*****************************************************************************\
+-----------------------------------------------------------------------------+
| SchoolReg                                                                   |
| Copyright (c) 2010 Z.                                                       |
| Copyright (c) 2009 Sergey V. Kuzin <sergey@kuzin.name>                      |
| All rights reserved.                                                        |
+-----------------------------------------------------------------------------+
| Some parts was taken from Drupal by Z                                       |
\*****************************************************************************/

#
# $Id: mysql_db.php 4 2010-02-02 18:52:58Z kuzmich $
#
/**
 * Indicates the place holders that should be replaced in _db_query_callback().
 */
define('DB_QUERY_REGEXP', '/(%d|%s|%%|%f|%b|%n)/');
/**
 * Helper function for db_query().
 */
function _db_query_callback($match, $init = FALSE) {
  static $args = NULL;
  if ($init) {
    $args = $match;
    return;
  }

  switch ($match[1]) {
    case '%d': // We must use type casting to int to convert FALSE/NULL/(TRUE?)
      $value = array_shift($args);
      // Do we need special bigint handling?
      if ($value > PHP_INT_MAX) {
        $precision = ini_get('precision');
        @ini_set('precision', 16);
        $value = sprintf('%.0f', $value);
        @ini_set('precision', $precision);
      }
      else {
        $value = (int) $value;
      }
      // We don't need db_escape_string as numbers are db-safe.
      return $value;
    case '%s':
      return mysql_real_escape_string(array_shift($args));
    case '%n':
      // Numeric values have arbitrary precision, so can't be treated as float.
      // is_numeric() allows hex values (0xFF), but they are not valid.
      $value = trim(array_shift($args));
      return is_numeric($value) && !preg_match('/x/i', $value) ? $value : '0';
    case '%%':
      return '%';
    case '%f':
      return (float) array_shift($args);
    case '%b': // binary data
      return db_encode_blob(array_shift($args));
  }
}
/**
 * Returns a properly formatted Binary Large OBject value.
 *
 * @param $data
 *   Data to encode.
 * @return
 *  Encoded data.
 */
function db_encode_blob($data) {
  return "'". mysql_real_escape_string($data) ."'";
}

function db_connect($host, $user, $passwd, $base)
{
  $link = @mysql_connect($host, $user, $passwd) or die('MySQL Error:<br />'.mysql_error());
  @mysql_select_db($base, $link) or die('MySQL Error:<br />'.mysql_error());
  @mysql_query('SET NAMES UTF8;') or die('MySQL Error:<br />'.mysql_error());
}

function db_query($sql) {
  $args = func_get_args();
  array_shift($args);
  if (isset($args[0]) and is_array($args[0])) { // 'All arguments in one array' syntax
    $args = $args[0];
  }
  _db_query_callback($args, TRUE);
  $sql = preg_replace_callback(DB_QUERY_REGEXP, '_db_query_callback', $sql);
	
  $result = @mysql_query($sql) or die('MySQL Error:<br />'.mysql_error());
  return $result;
}

function db_get_first_row($sql)
{
  $result = @mysql_query($sql) or die('MySQL Error:<br />'.mysql_error());
  $row = mysql_fetch_assoc($result);
  return $row;
}

function db_get_cell($sql)
{
  $result = @mysql_query($sql) or die('MySQL Error:<br />'.mysql_error());
  $row = mysql_fetch_assoc($result);
  if ($row) {
    return array_pop($row);
  } else {
  	return null;
  }
}

function db_get_insert_id()
{
  return mysql_insert_id();
}

function db_array2insert($table, $data)
{
  $fields = implode(', ', array_keys($data));
  $values = "'".implode("', '", array_map('mysql_escape_string', $data))."'";
  $sql = "INSERT INTO $table ($fields) VALUES ($values);";
  db_query($sql);
}

function db_array2update($table, $data, $where)
{
	$fields = array();
	foreach($data as $field=>$value) {
    $fields[] = $field.' = \''.mysql_escape_string($value).'\'';
 }
  $sql = "UPDATE  $table SET ".implode(', ', $fields)." WHERE $where;";
  db_query($sql);
}

?>