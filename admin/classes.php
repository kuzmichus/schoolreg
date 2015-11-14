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

define('ADMIN_ZONE', true);

include_once ('../init.php');
include_once ('../include/classes.php');
include_once ('../include/curriculums.php');

$school_years = get_school_years() or die("Вы должны добавить хотя бы один учебный год.");
$school_year_id = intval(@$_REQUEST['school_year_id']);
$curr_year = null;
if ($school_year_id == 0) { //assign $school_year_id to current period or to the last one 
	$cur_time = time();
	foreach ($school_years as $year){
		if (strtotime($year['started']) < $cur_time and $cur_time < strtotime($year['finished']) ) {
			break;			
		}
	}
	$school_year_id = $year['school_year_id'];
	$curr_year = $year;
}
$classes_list = get_classes_list($school_year_id);
if (!isset($curr_year)) {
	$curr_year = get_school_year($school_year_id);
}
$template_name = isAjax()?'classes-ajax.html':'classes.html';
$renderArray = array('school_year_id'=>$school_year_id,'school_years'=>$school_years, 'classes_list'=>$classes_list);
$renderArray['current_year'] = $curr_year;
$renderArray['ui_theme'] = $config['ui']['theme'];
$template = $twig->loadTemplate('admins/'.$template_name);
echo $template->render($renderArray);  
?>
