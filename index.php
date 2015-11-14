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
# $Id: index.php 4 2010-02-02 18:52:58Z kuzmich $
#

define('STUDENT_ZONE', true);
include_once ('init.php');
include_once ('include/students.php');
include_once ('include/classes.php');
include_once ('include/curriculums.php');

$renderArray = array();
$fromAjax = intval($_REQUEST['fromAjax']);
$template_name = 'index.html';

if ($fromAjax) {
	$template_name = 'index-ajax.html';
	$quarter_id = intval($_REQUEST['quarter_id']) or die('Invalid quarter_id');
	$quarter = get_quarter($quarter_id) or die("No quarter for $quarter_id=$quarter_id");
	$date_from = $quarter['started'];
	$date_to = $quarter['finished'];
	$day_of_week = array(0 => 'Пн', 1 => 'Вт'
	, 2 => 'Ср', 3 => 'Чт'
	, 4 => 'Пт', 5 => 'Сб', 6 => 'Вс');
	$class_id = get_student_class_id($student_id) or die('Unknown student');
	$student_grades = get_student_grades($student_id, $date_from, $date_to);
	$disciplines = get_disciplines_from_class($class_id);
	$dates = array();
	$grades = array();
	foreach ($student_grades as $grade) {
		$lesson_monthyear = $grade['lesson_month'].'.'.$grade['lesson_year'];
		$dates[$lesson_monthyear][$grade['lesson_date']] = array('lesson_weekday' 	=> $grade['lesson_weekday']
		, 'lesson_weekday_w'	=> $day_of_week[$grade['lesson_weekday']]
		, 'lesson_day' 		=> $grade['lesson_day']
		, 'lesson_month' 	=> $grade['lesson_month']
		, 'lesson_year' 		=> $grade['lesson_year']);
		$grades[$grade['discipline_id']][$grade['lesson_date']][] = array('grade'=>$grade['grade'],
		'topic' => $grade['topic'], 'lesson_id'=> $grade['lesson_id'],
		'lesson_type_id' => $grade['lesson_type_id'], 'lesson_type_name' => $grade['lesson_type_name']);
	}
	$quarter['started_date'] = date("d.m.Y",strtotime($quarter['started']));
	$quarter['finished_date'] = date("d.m.Y",strtotime($quarter['finished']));
	$renderArray['quarter'] = $quarter;
	$renderArray['dates'] = $dates;
	$renderArray['disciplines'] = $disciplines;
	$renderArray['grades'] = $grades;
} else {
	$year = get_current_year() or die('Now current year');
	$quarters = get_quarters_in_year($year['school_year_id'],1);
	$renderArray['ui_theme'] = $config['ui']['theme'];
	$renderArray['year'] = $year;
	$renderArray['quarters'] = $quarters;
}

$template = $twig->loadTemplate('students/'.$template_name);
echo $template->render($renderArray);
?>
