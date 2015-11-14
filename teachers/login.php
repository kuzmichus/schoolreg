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
# $Id: login.php 4 2010-02-02 18:52:58Z kuzmich $
#

define('TEACHER_ZONE_LOGIN', true);
include_once ('../init.php');
include_once ('../include/teachers.php');

$loginError = '';
if (isset($_REQUEST['action'])) {
  $action = $_REQUEST['action'];
  if ($action == 'login') {
  	$teacher = teacher_login(substr($_POST['login'], 0, 25), $_POST['passwd']);
  	
    if ($teacher and intval($teacher['teacher_id']) != 0) {
    	$_SESSION['teacher_id'] = $teacher['teacher_id'];
    	header('Location: index.php');
    	exit();
    } else {
    	$loginError = 'Пароль/логин неверен';
    }
  } elseif ($action == 'logout') {
    unset($_SESSION['teacher_id']);
    header('Location: index.php');
    exit();
  }

}

$renderArray = array();
$template_name = 'login.html';
$renderArray['loginError']=$loginError;

$template = $twig->loadTemplate('teachers/'.$template_name);
echo $template->render($renderArray);
?>
