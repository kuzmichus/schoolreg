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
# $Id: add_class.php 4 2010-02-02 18:52:58Z kuzmich $
#


define('ADMIN_ZONE', true);

include_once ('../init.php');
include_once ('../include/teachers.php');
include_once ('../include/classes.php');

$renderArray['class_numbers'] = $config['class']['numbers'];
$renderArray['class_letters'] = $config['class']['letters'];

if (isset($_REQUEST['action']) and $_REQUEST['action'] == 'add') {
	$res = add_new_class(intval($_REQUEST['class']),$_REQUEST['letter'],intval($_REQUEST['school_year_id']), intval($_REQUEST['teacher_id']));
	echo json_encode($res);
	
} else {
	$teachers = get_teachers_list();
	$school_year_id = intval(@$_REQUEST['school_year_id']) or die('Invalid call');
	$template_name = 'add_class-ajax.html';
	$renderArray['teachers'] = $teachers;
	$renderArray['school_year_id'] = $school_year_id;
	$template = $twig->loadTemplate('admins/'.$template_name);
	echo $template->render($renderArray);
}

?>
