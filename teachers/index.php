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
# $Id: index.php 4 2010-02-02 18:52:58Z kuzmich $
#

define('TEACHER_ZONE', true);
include_once ('../init.php');
include_once ('../include/classes.php');

$renderArray = array();
$classes_list = get_classe_list_from_teacher($teacher_id);
$template_name = 'index.html';
$renderArray['classes_list']=$classes_list;

$template = $twig->loadTemplate('teachers/'.$template_name);
echo $template->render($renderArray);
?>
