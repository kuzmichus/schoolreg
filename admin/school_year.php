<?php
/*****************************************************************************\
+-----------------------------------------------------------------------------+
| SchoolReg                                                                   |
| Copyright (c) 2009 Sergey V. Kuzin <sergey@kuzin.name>                      |
| All rights reserved.                                                        |
+-----------------------------------------------------------------------------+
\*****************************************************************************/

#
# $Id: school_year.php 4 2010-02-02 18:52:58Z kuzmich $
#

define('ADMIN_ZONE', true);

include_once ('../init.php');
include_once ('../include/curriculums.php');

if (isset($_REQUEST['school_year_id'])) {
	$school_year_id = intval($_REQUEST['school_year_id']);
} else {
  $school_year_id = 0;
}




$mode = @$_REQUEST['mode'];

if ($school_year_id == 0 && $mode == '') {
	$mode = 'add';
} elseif ($school_year_id != 0 && $mode == '') {
	$mode = 'update';
}



if (isset($_REQUEST['action'])) {
  $action = $_REQUEST['action'];

  if ($action == 'add') {

    $fields[] = "name_year='". mysql_escape_string(substr($_POST['name_year'], 0, 50))."'";
    $fields[] = "started='".implode('-', array_reverse(explode('.', $_POST['started'])))."'";
    $fields[] = "finished='".implode('-', array_reverse(explode('.', $_POST['finished']))  )."'";

    db_query($sql = "INSERT school_years SET ".implode(', ', $fields));
    header('Location: school_year.php?mode=success_add');
    exit();

  } elseif ($action == 'update') {
    $fields = array();

    $fields[] = "name_year='". mysql_escape_string(substr($_POST['name_year'], 0, 50))."'";
    $fields[] = "started='".implode('-', array_reverse(explode('.', $_POST['started'])))."'";
    $fields[] = "finished='".implode('-', array_reverse(explode('.', $_POST['finished']))  )."'";


    db_query($sql = "UPDATE school_years SET ".implode(', ', $fields).' WHERE school_year_id='.$school_year_id);
    header('Location: school_year.php?mode=success_update');
    exit();

  }
}
  include('../header_dialog.php');
?>

  <body style="margin-left: 0px;	margin-right: 0px;">
<?php

  if ($mode == 'success_update') {
  	echo '<center>Информация о периоде успешно обновлена.<br /><br />';
  	echo '<input type="button" value="&nbsp;&nbsp;Закрыть&nbsp;&nbsp;" onclick="self.parent.tb_remove();self.parent.location.reload();" /></center>';
  } elseif ($mode == 'success_add') {
  	echo '<center>Новый учебный период добавлен.<br /><br />';
  	echo '<input type="button" value="&nbsp;&nbsp;Закрыть&nbsp;&nbsp;" onclick="self.parent.tb_remove();self.parent.location.reload();" />
  	&nbsp;&nbsp;<input type="button" value="&nbsp;&nbsp;Продолжить&nbsp;&nbsp;" onclick="document.location=\'school_year.php\'" /></center>';
  } elseif ($mode == 'update') {
    $year  = get_school_year($school_year_id);
    outYearForm($year  );
  } elseif ($mode == 'add') {
    outYearForm();
  } elseif ($mode == 'drop') {
    dropYearForm();
  }

function outYearForm($year = null)
{
	global $school_year_id, $quarter_id;
	echo '
<form action="school_year.php" method="post">';
if (isset($year)) {
  echo '<input type="hidden" name="action" value="update" />';
  echo '<input type="hidden" name="school_year_id" value="'.$school_year_id.'" />';
} else {
  echo '<input type="hidden" name="action" value="add" />';
}
echo '

<script type="text/javascript">
	jQuery(function($){

	$.mask.definitions[\'~\']=\'[01]\';
	$.mask.definitions[\'a\']=\'[0123]\';
	$.mask.definitions[\'b\']=\'[12]\';
  $.mask.definitions[\'c\']=\'[09]\';
  $("#started_id").mask("a9.~9.bc99");
  $("#finished_id").mask("a9.~9.bc99");

  });
</script>
<table width="100%" id="edit_in">
<tbody>
  <tr>
    <td>Название<font color="red">*</font></td>
    <td><input type="text" name="name_year" id="name_year_id" size="50" value="'.(isset($year)?$year['name_year']:'').'" /></td>
  </tr>
<tr><td colspan="2">&nbsp;</td></tr>
  <tr>
    <td>Начало<font color="red">*</font></td>
    <td><input type="text" name="started" id="started_id" value="'.(isset($year)?date('d.m.Y', strtotime($year['started'])):'').'" /></td>
  </tr>
  <tr>
    <td>Окончание<font color="red">*</font></td>
    <td><input type="text" name="finished" id="finished_id" value="'.(isset($year)?date('d.m.Y', strtotime($year['finished'])):'').'" /></td>
  </tr>



  <tr>
    <td colspan="2" align="center">';
if (isset($year)) {
	echo '<input type="submit" class="button" value="Сохранить" >';
} else {
  echo '<input type="submit" class="button" value="Добавить" >';
}
echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="button" class="button" value="&nbsp;&nbsp;Закрыть&nbsp;&nbsp;" onclick="self.parent.tb_remove()" /></td>
</tr></tbody></table></form>';
}

function dropYearForm()
{
	global $school_year_id, $quarter_id;
	echo '<form action="school_year.php" method="post">
<input type="hidden" name="action" value="drop" />
<input type="hidden" name="school_year_id" value="'.$school_year_id.'" />
<input type="submit" value="Да" />';
}

?>
  </body>
</html>