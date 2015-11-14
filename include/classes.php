<?php
/*****************************************************************************\
+-----------------------------------------------------------------------------+
| SchoolReg                                                                   |
| Copyright (c) 2010 Z.                                                       |
| Copyright (c) 2009 Sergey V. Kuzin <sergey@kuzin.name>                      |
| All rights reserved.                                                        |
+-----------------------------------------------------------------------------+
\*****************************************************************************/

#
# $Id: classes.php 4 2010-02-02 18:52:58Z kuzmich $
#

function add_new_class($p_class, $p_letter, $p_school_year_id, $p_teacher_id) {
	$retArray = array('isError' => false);
	//check if class alresy exists
	$sql = "SELECT class_id FROM classes WHERE class=%n AND letter='%s' AND school_year_id=%n";
	$res = db_query($sql, $p_class, $p_letter, $p_school_year_id);
	$row = mysql_fetch_row($res);
	if ($row) {
		$retArray['isError'] = true;
		$retArray['errorText'] = 'Класс с такимим параметрами уже существует.'; 
	} else {
		$sql = "INSERT INTO classes (class, letter, school_year_id, teacher_id) VALUES(%n,'%s',%n,%n)";
		$res = db_query($sql, $p_class, $p_letter, $p_school_year_id, $p_teacher_id);
		if (!res) {
			$retArray['isError'] = true;
			$retArray['errorText'] = "INSERT MySQL error ".mysql_errno().": ".mysql_error();
		} else {
			$retArray['newClassId'] = db_get_insert_id();
		}
	}
	return $retArray;
}

function get_classes_list($school_year_id = null)
{
  $sql = "SELECT class_id, CONCAT_WS('', class, '-', letter) AS name, class, letter, CONCAT_WS('', last_name, ' ', SUBSTRING(first_name, 1, 1), '. ', SUBSTRING(middle_name, 1, 1),'.') AS teacher_name, school_year FROM classes LEFT JOIN teachers ON classes.teacher_id=teachers.teacher_id";
  $params = array();
  if (!is_null($school_year_id)) {
    $sql .= ' WHERE school_year_id=%n';
    $params[] = $school_year_id;
  }
  $sql .= ' ORDER BY class, letter';
  $res = db_query($sql,$params);
  $list_classes = array();
  while($row = mysql_fetch_assoc($res)){
    $list_classes[] = $row;
  }
  return $list_classes;
}

function get_student_classes_list($class_id, $expeled = null)
{
	$sql = "SELECT students.student_id, CONCAT_WS('', last_name, ' ', first_name, ' ', SUBSTRING(middle_name, 1, 1),'.') AS student_name FROM students_in_class LEFT JOIN  students ON students_in_class.student_id=students.student_id";
	$sql .= ' WHERE class_id=%n';
  	$params = array($class_id);
	if (!is_null($expeled)) {
		$sql  .= ' AND expeled=%n';
		$params[] = $expeled;
	}
	$sql .= ' ORDER BY student_name';
	$res = db_query($sql, $params);
	$list_classes = array();
	while($row = mysql_fetch_assoc($res)){
		$list_classes[] = $row;
	}
	return $list_classes;
}

function get_classe_list_from_teacher($teacher_id)
{
  $sql = "SELECT classes.class_id, subject_id, class, letter, discipline
FROM classes
LEFT JOIN subjects ON classes.class_id = subjects.class_id
LEFT JOIN disciplines ON subjects.discipline_id=disciplines.discipline_id
WHERE subjects.teacher_id=".$teacher_id.' ORDER BY class, letter, discipline';
  $res = db_query($sql);
  $list_classes = array();
  while($row = mysql_fetch_assoc($res)){
    $list_classes[] = $row;
  }
  return $list_classes;
}

function get_grade_from_lesson($student_id, $subject_id)
{
	$res = db_query('SELECT * FROM lessons WHERE subject_id='.intval($subject_id));
  $grades = array();
  while ($row = mysql_fetch_assoc($res)) {
  	$grades[$row['lesson_id']] = 0;
  }

	$res = db_query('SELECT * FROM students_on_lesson WHERE student_id='.intval($student_id));
  while ($row = mysql_fetch_assoc($res)) {
  	$grades[$row['lesson_id']] = $row['grade'];
  }
  return $grades;
}

function get_disciplines_from_class($class_id)
{
	$sql = 'SELECT disciplines.discipline_id, discipline FROM disciplines'
	      .' INNER JOIN subjects ON subjects.discipline_id = disciplines.discipline_id'
	      .' WHERE subjects.class_id=%n'
	      .' ORDER BY discipline';
	$res = db_query($sql,intval($class_id));
  	$disciplines = array();
  	while ($row = mysql_fetch_assoc($res)) {
	  	$disciplines[$row['discipline_id']] = $row['discipline'];
  	}
  	return $disciplines;
}

?>