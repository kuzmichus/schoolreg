<?php
/*****************************************************************************\
+-----------------------------------------------------------------------------+
| SchoolReg                                                                   |
| Copyright (c) 2010 Z.                                                       |
| Copyright (c) 2009 Sergey V. Kuzin <sergey@kuzin.name>                      |
| All rights reserved.                                                        |
+-----------------------------------------------------------------------------+
\*****************************************************************************/


define('TEACHER_ZONE', true);
include_once ('../init.php');
include_once ('../include/classes.php');
include_once ('../include/lessons.php');

$subject_id = intval($_REQUEST['subject_id']) or die('No subject_id parameter');
$fromAjax = intval($_REQUEST['fromAjax']);

$action = $_REQUEST['action'];
$queryResult = '';
if ($action == 'close') {
	$grades = $_POST['grades'];
	update_lesson_grades($grades,true);
	$queryResult = "Уроки сохранены и закрыты.";
} elseif ($action == 'update') {
	$grades = $_POST['grades'];
	update_lesson_grades($grades,false);
	$queryResult = "Уроки обновлены.";;
} elseif ($action == 'addlesson') {
  	$lesson_date = implode('-', array_reverse(explode('.', $_REQUEST['lesson_date'])));
  	$topic = addslashes($_REQUEST['topic']);
  	$lesson_type_id = intval($_REQUEST['lesson_type_id']);
  	$res = add_lesson($lesson_date, $subject_id, $topic, $lesson_type_id);
  	if ($res) {
  		$queryResult = "Новый урок успешно добавлен.";
  	} else {
  		$queryResult = "Ошибка при добавлении урока.";
  	}
} elseif ($action == 'changeDates') {
	$_SESSION['date_from'] = $_REQUEST['lesson_date_from'];
	$_SESSION['date_to'] = $_REQUEST['lesson_date_to'];
}

if ( !isset($_SESSION['date_from']) or $_SESSION['date_from'] == '') {
	$_SESSION['date_from'] = date("d.m.Y", strtotime('-10 days',time()));
}
if ( !isset($_SESSION['date_to']) or $_SESSION['date_to'] == '') {
	$_SESSION['date_to'] = date("d.m.Y");
}

$date_from = implode('-', array_reverse(explode('.', $_SESSION['date_from'])));
$date_to = implode('-', array_reverse(explode('.', $_SESSION['date_to'])));
$template_name = 'class_lessons.html';
$renderArray = array('date_from'=>$_SESSION['date_from'], 'date_to'=> $_SESSION['date_to'], 'ui_theme'=>$config['ui']['theme']);
$renderArray['subject_id']=$subject_id;
$renderArray['queryResult']=$queryResult;

if ($fromAjax) {
	$template_name = 'class_lessons_ajax.html';
	$subject = get_lesson_subject($subject_id);
	$lessons = get_lessons($subject_id, $date_from, $date_to);
	$lessons_count = count($lessons);
	$students_list = get_student_classes_list($subject['class_id'],0);
	$active_leson = false;
	foreach ($students_list as $key => $student) {
		$students_list[$key]['grades'] = get_student_lesson_grades($student['student_id'], $subject_id, $date_from, $date_to); 
	}
	
	$renderArray['lessons']=$lessons;
	$renderArray['lessons_count']=$lessons_count;
	$renderArray['students_list']=$students_list;
} else {
	$renderArray['lesson_types'] = get_lesson_types();
}

$template = $twig->loadTemplate('teachers/'.$template_name);
echo $template->render($renderArray);
?>
