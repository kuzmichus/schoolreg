<?php
/*****************************************************************************\
+-----------------------------------------------------------------------------+
| SchoolReg                                                                   |
| Copyright (c) 2010 Z.                                                       |
| Copyright (c) 2009 Sergey V. Kuzin <sergey@kuzin.name>                      |
| All rights reserved.                                                        |
+-----------------------------------------------------------------------------+
\*****************************************************************************/
function add_lesson($lesson_date, $subject_id, $lesson_topic, $lesson_type_id) {
	$sql = "INSERT INTO lessons (lesson_date, subject_id, topic, active, lesson_type_id) VALUES('%s',%n,'%s',%n,%n)";
	$res = db_query($sql, $lesson_date, $subject_id, $lesson_topic, (time() + 60*60*45), $lesson_type_id);
	return db_get_insert_id();
}

function get_lesson_types() {
	$query = "SELECT * FROM lesson_types ORDER BY lesson_type_id";
	$result = db_query($query);
	$lesson_types = array();
	while($row = mysql_fetch_assoc($result)){
		$lesson_types[] = $row; 
	}
	return $lesson_types;
}

function get_lessons($subject_id, $date_from, $date_to)
{
	$query = "SELECT l.*, DATE_FORMAT(l.lesson_date,'%%d.%%m') lesson_date_short, lt.lesson_type_name "
			.'  FROM lessons l '
			.'  LEFT JOIN lesson_types lt ON lt.lesson_type_id=l.lesson_type_id'
			.'  WHERE subject_id=%n '
			."    AND l.lesson_date BETWEEN '%s' AND '%s'"
			.'  ORDER BY lesson_date, lesson_id';
	$result = db_query($query, $subject_id, $date_from, $date_to);
	$lessons = array();
	while($row = mysql_fetch_assoc($result)){
		$lessons[] = $row; 
	}
	return $lessons;
}

function get_student_lesson_grades($student_id, $subject_id, $date_from, $date_to)
{
	$query = 'SELECT l.*, sl.grade, lt.lesson_type_name '
			.'  FROM lessons l '
			.'  LEFT JOIN lesson_types lt ON lt.lesson_type_id=l.lesson_type_id '
			.'  LEFT JOIN students_on_lesson sl ON sl.lesson_id=l.lesson_id  AND sl.student_id=%n '
			.'WHERE l.subject_id=%n '
			."  AND l.lesson_date BETWEEN '%s' AND '%s'"
			.'  ORDER BY lesson_date, lesson_id';
	$result = db_query($query, $student_id, $subject_id, $date_from, $date_to);
	$grades = array();
	while($row = mysql_fetch_assoc($result)){
		$grades[] = $row; 
	}
	return $grades;
}

function get_lesson_subject($subject_id) 
{
	$query = 'SELECT * FROM subjects WHERE subject_id=%n';
	$result = db_query($query, $subject_id);
	$row = mysql_fetch_assoc($result);
	return $row;
}

function update_lesson_grades($grades, $doClose) {
	foreach ($grades as $lesson_id => $lesson) {
      db_query('DELETE FROM students_on_lesson WHERE lesson_id=%n',$lesson_id);
      foreach ($lesson as $student_id => $grade) {
      	$gradeval = @intval($grade);
      	if ($gradeval) {
      		db_query('INSERT INTO students_on_lesson (student_id, lesson_id, grade) VALUES(%n,%n,%n)',$student_id,$lesson_id,$gradeval);
      	}
      }
      if ($doClose) {
      	db_query('UPDATE lessons SET active=0 WHERE lesson_id=%n',$lesson_id);
      }
  	}
}
