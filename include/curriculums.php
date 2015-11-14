<?php
/*****************************************************************************\
+-----------------------------------------------------------------------------+
| SchoolReg                                                                   |
| Copyright (c) 2010 Z.
| Copyright (c) 2009 Sergey V. Kuzin <sergey@kuzin.name>                      |
| All rights reserved.                                                        |
+-----------------------------------------------------------------------------+
\*****************************************************************************/

#
# $Id: curriculums.php 11 2010-03-20 21:11:22Z kuzmich $
#

function get_school_years()
{
  $sql = "SELECT school_year_id, name_year, current, started, finished FROM school_years ORDER BY started";
  $res = db_query($sql);
  $school_years = array();
  while($row = mysql_fetch_assoc($res)){
    $school_years[] = $row;
  }
  return $school_years;
}

function get_school_year($school_year_id)
{
  $sql = "SELECT school_year_id, name_year, current, started, finished FROM school_years WHERE school_year_id = $school_year_id";
  $res = db_query($sql);
  $school_year = mysql_fetch_assoc($res);
  return $school_year;
}


function get_quarters_in_year($school_year_id, $quarter_type = null)
{
	$sql = 'SELECT * FROM quarters q WHERE q.school_year_id=%n';
	$params = array($school_year_id);
	if (!is_null($quarter_type)) {
	  $sql  .= ' AND q.quarter_type=%n';
	  $params[] = $quarter_type;
	}
	$res = db_query($sql,$params);
	if (!$res) {
		return false;
	}
	$quarters_in_year = array();
	while($row = mysql_fetch_assoc($res)){
		$quarters_in_year[] = $row;
	}
	return $quarters_in_year;
}

function get_quarter($quarter_id)
{
  $sql = "SELECT * FROM quarters WHERE quarter_id = %n";
  $res = db_query($sql, $quarter_id);
  $quarter = mysql_fetch_assoc($res);
  return $quarter;
}

function get_current_year() {
  $sql = "SELECT * FROM school_years sy WHERE DATE(NOW()) BETWEEN started AND finished";
  $res = db_query($sql);
  if (!$res) {
	return false;
  }
  $school_year = mysql_fetch_assoc($res);
  return $school_year;
}
?>