<?php
/*****************************************************************************\
+-----------------------------------------------------------------------------+
| SchoolReg                                                                   |
| Copyright (c) 2010 Z.
| Copyright (c) 2009 Sergey V. Kuzin <sergey@kuzin.name>                      |
| All rights reserved.                                                        |
+-----------------------------------------------------------------------------+
\*****************************************************************************/

include_once ('init.php');
include_once ('include/students.php');

$loginError = '';
if (isset($_REQUEST['action'])) {
  $action = $_REQUEST['action'];
  if ($action == 'login') {
  	$pin = intval($_POST['pin_code']);
  	$student = student_login($pin);
    if ($student and intval($student['student_id']) != 0) {
    	$_SESSION['student_id'] = $student['student_id'];
    	header('Location: index.php');
    	exit();
    } else {
    	$loginError = 'ПИН неверен';
    }
  } elseif ($action == 'logout') {
    unset($_SESSION['student_id']);
    header('Location: index.php');
    exit();
  }

}

$renderArray = array();
$template_name = 'login.html';
$renderArray['loginError']=$loginError;

$template = $twig->loadTemplate('students/'.$template_name);
echo $template->render($renderArray);
?>
