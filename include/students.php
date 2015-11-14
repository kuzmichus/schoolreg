<?php
/*****************************************************************************\
+-----------------------------------------------------------------------------+
| SchoolReg                                                                   |
| Copyright (c) 2009 Sergey V. Kuzin <sergey@kuzin.name>                      |
| All rights reserved.                                                        |
+-----------------------------------------------------------------------------+
\*****************************************************************************/

#
# $Id: students.php 4 2010-02-02 18:52:58Z kuzmich $
#

function get_student($student_id)
{
  $sql = "SELECT * FROM students WHERE student_id = $student_id ORDER BY 1, 2, 3";
  $res = db_query($sql);
  $users_list = array();
  return mysql_fetch_assoc($res);
}

function student_login($pincode) {
	$sql = "SELECT student_id FROM students WHERE pin_code=%n and pin_code>0";
	$res = db_query($sql,$pincode);
	if (!$res) {
		return false;
	}
	$row = mysql_fetch_assoc($res);
	return $row;
}

function get_student_class_id($student_id) {
	$sql = 'SELECT class_id FROM students_in_class WHERE student_id=%n';
	$result = db_query($sql,$student_id);
	if (!$result) {
		return false;
	}
	$row = mysql_fetch_assoc($result);
	return $row['class_id'];
}

function get_student_grades($student_id, $date_from, $date_to) {
	$sql = <<<EOS
SELECT
  students_in_class.student_id AS student_id,
  lessons.lesson_date          AS lesson_date,
  WEEKDAY(lessons.lesson_date) AS lesson_weekday,
  lessons.lesson_id            AS lesson_id,
  DATE_FORMAT(lessons.lesson_date,'%%d') AS lesson_day,
  DATE_FORMAT(lessons.lesson_date,'%%m') AS lesson_month,
  DATE_FORMAT(lessons.lesson_date,'%%Y') AS lesson_year,
  lessons.lesson_type_id       AS lesson_type_id,
  lt.lesson_type_name          AS lesson_type_name,
  subjects.discipline_id       AS discipline_id,
  disciplines.discipline       AS discipline,
  lessons.topic                AS topic,
  students_on_lesson.grade     AS grade
FROM students_in_class
  JOIN subjects
    ON subjects.class_id = students_in_class.class_id
  JOIN lessons
    ON lessons.subject_id = subjects.subject_id
  LEFT JOIN lesson_types lt ON lt.lesson_type_id=lessons.lesson_type_id
  LEFT JOIN students_on_lesson
    ON students_on_lesson.student_id = students_in_class.student_id
      AND students_on_lesson.lesson_id = lessons.lesson_id
  LEFT JOIN disciplines
    ON disciplines.discipline_id = subjects.discipline_id
WHERE grade IS NOT NULL
    AND students_in_class.student_id = %n
    AND lesson_date BETWEEN '%s' AND '%s'
ORDER BY lessons.lesson_date, disciplines.discipline, lessons.lesson_id
EOS;
	$result = db_query($sql, $student_id, $date_from, $date_to);
	if (!$result) {
		return false;
	}
	$array = array();
	while($row = mysql_fetch_assoc($result)){
		$array[] = $row; 
	}
	return $array;
}
?>