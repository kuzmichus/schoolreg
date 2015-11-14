<?php
/*****************************************************************************\
+-----------------------------------------------------------------------------+
| SchoolReg                                                                   |
| Copyright (c) 2009 Sergey V. Kuzin <sergey@kuzin.name>                      |
| All rights reserved.                                                        |
+-----------------------------------------------------------------------------+
\*****************************************************************************/

#
# $Id: quarter.php 4 2010-02-02 18:52:58Z kuzmich $
#

define('ADMIN_ZONE', true);

include_once ('../init.php');
include_once ('../include/curriculums.php');

if (isset($_REQUEST['school_year_id'])) {
	$school_year_id = intval($_REQUEST['school_year_id']);
} else {
  $school_year_id = 0;
}

if (isset($_REQUEST['quarter_id'])) {
	$quarter_id = intval($_REQUEST['quarter_id']);
} else {
  $quarter_id = 0;
}

$mode = @$_REQUEST['mode'];

if ($quarter_id == 0 && $mode == '') {
	$mode = 'add';
} elseif ($quarter_id != 0 && $mode == '') {
	$mode = 'update';
}



if (isset($_REQUEST['action'])) {
  $action = $_REQUEST['action'];

  if ($action == 'add') {

    $fields[] = "quarter_name='". mysql_escape_string(substr($_POST['quarter_name'], 0, 50))."'";
    $fields[] = "started='".implode('-', array_reverse(explode('.', $_POST['started'])))."'";
    $fields[] = "finished='".implode('-', array_reverse(explode('.', $_POST['finished']))  )."'";
    $fields[] = "quarter_type=".intval($_POST['quarter_type']);
    $fields[] = "school_year_id=".$school_year_id;

    db_query("INSERT quarters SET ".implode(', ', $fields));
    header('Location: quarter.php?mode=success_add');
    exit();

  } elseif ($action == 'update') {
    $fields = array();

    $fields[] = "quarter_name='". mysql_escape_string(substr($_POST['quarter_name'], 0, 50))."'";
    $fields[] = "started='".implode('-', array_reverse(explode('.', $_POST['started'])))."'";
    $fields[] = "finished='".implode('-', array_reverse(explode('.', $_POST['finished']))  )."'";
    $fields[] = "quarter_type=".intval($_POST['quarter_type']);


    db_query($sql = "UPDATE quarters SET ".implode(', ', $fields).' WHERE quarter_id='.$quarter_id);
    header('Location: quarter.php?mode=success_update');
    exit();

  }
}
  include('../header_dialog.php');
?>

  <body style="margin-left: 0px;	margin-right: 0px;">
<?php

  if ($mode == 'success_update') {
  	echo '<center>Информация о четверте успешно обновлена.<br /><br />';
  	echo '<input type="button" value="&nbsp;&nbsp;Закрыть&nbsp;&nbsp;" onclick="self.parent.tb_remove();self.parent.location.reload();" /></center>';
  } elseif ($mode == 'success_add') {
  	echo '<center>Новая четверть успешно добавлен.<br /><br />';
  	echo '<input type="button" value="&nbsp;&nbsp;Закрыть&nbsp;&nbsp;" onclick="self.parent.tb_remove();self.parent.location.reload();" />
  	&nbsp;&nbsp;<input type="button" value="&nbsp;&nbsp;Продолжить&nbsp;&nbsp;" onclick="document.location=\'school_year.php\'" /></center>';
  } elseif ($mode == 'update') {
    $quarter  = get_quarter($quarter_id);
    outQuarterForm($quarter);
  } elseif ($mode == 'add') {
    outQuarterForm();
  }

function outQuarterForm($quarter = null)
{
	global $school_year_id, $quarter_id;

	echo '
<form action="quarter.php" method="post">';
if (isset($quarter)) {
	echo '<input type="hidden" name="action" value="update" />';
  echo '<input type="hidden" name="quarter_id" value="'.$quarter_id.'" />';
} else {
  echo '<input type="hidden" name="action" value="add" />';
}
echo '<input type="hidden" name="school_year_id" value="'.$school_year_id.'" />';
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
    <td><input type="text" name="quarter_name" id="quarter_name_id" size="50" value="'.(isset($quarter)?$quarter['quarter_name']:'').'" /></td>
  </tr>
<tr><td colspan="2">&nbsp;</td></tr>
  <tr>
    <td>Начало<font color="red">*</font></td>
    <td><input type="text" name="started" id="started_id" value="'.(isset($quarter)?date('d.m.Y', strtotime($quarter['started'])):'').'" /></td>
  </tr>
  <tr>
    <td>Окончание<font color="red">*</font></td>
    <td><input type="text" name="finished" id="finished_id" value="'.(isset($quarter)?date('d.m.Y', strtotime($quarter['finished'])):'').'" /></td>
  </tr>

  <tr>
    <td>Тип<font color="red">*</font></td>
    <td><select name="quarter_type" id="quarter_type_id">
     <option value="1"'.(isset($quarter)?($quarter['quarter_type']==1?' selected="selected"':''):'').'>Учеба</option>
     <option value="2"'.(isset($quarter)?($quarter['quarter_type']==2?' selected="selected"':''):'').'>Каникулы</option>
     </select></td>
  </tr>



  <tr>
    <td colspan="2" align="center">';
if (isset($quarter)) {
	echo '<input type="submit" class="button" value="Сохранить" >';
} else {
  echo '<input type="submit" class="button" value="Добавить" >';
}
echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="button" class="button" value="&nbsp;&nbsp;Закрыть&nbsp;&nbsp;" onclick="self.parent.tb_remove()" /></td>
</tr></tbody></table></form>';
}

?>
  </body>
</html>