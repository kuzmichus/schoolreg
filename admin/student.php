<?php
/*****************************************************************************\
+-----------------------------------------------------------------------------+
| SchoolReg                                                                   |
| Copyright (c) 2009 Sergey V. Kuzin <sergey@kuzin.name>                      |
| All rights reserved.                                                        |
+-----------------------------------------------------------------------------+
\*****************************************************************************/


#
# $Id: student.php 19 2010-04-04 08:57:11Z kuzmich $
#

define('ADMIN_ZONE', true);

include_once ('../init.php');
include_once ('../include/classes.php');


$class_id   = @intval($_REQUEST['class_id']);
$student_id = @intval($_REQUEST['student_id']);
$mode       = @$_REQUEST['mode'];



if (isset($_REQUEST['action'])) {
  $action = $_REQUEST['action'];

  if ($action == 'student') {
    include_once ('../header_dialog.php');
    $result = mysql_query('SELECT * FROM students WHERE student_id='.$student_id);
    $student = mysql_fetch_assoc($result);
    echo json_encode($student);
    exit;
  }

  if ($action == 'add') {
    /* Информация об ученике*/
    $fields[] = "last_name='". mysql_escape_string(substr($_POST['last_name'], 0, 25))."'";
    $fields[] = "first_name='".mysql_escape_string(substr($_POST['first_name'], 0, 25))."'";
    $fields[] = "middle_name='".mysql_escape_string(substr($_POST['middle_name'], 0, 25))."'";
    $fields[] = "birthday='".mysql_escape_string(implode('-', array_reverse(explode('.', $_POST['birthday']))))."'";
    $fields[] = "address='".mysql_escape_string(substr($_POST['address'], 0, 255))."'";
    $fields[] = "phone='".mysql_escape_string(substr($_POST['phone'], 0, 25))."'";

    /* Информация о родителях*/
    $fields[] = "mother_fio='".mysql_escape_string(substr($_POST['mother_fio'], 0, 50))."'";
    $fields[] = "mother_work_phone='".mysql_escape_string(substr($_POST['mother_work_phone'], 0, 25))."'";
    $fields[] = "mother_cell_phone='".mysql_escape_string(substr($_POST['mother_cell_phone'], 0, 25))."'";

    $fields[] = "father_fio='".mysql_escape_string(substr($_POST['father_fio'], 0, 50))."'";
    $fields[] = "father_work_phone='".mysql_escape_string(substr($_POST['father_work_phone'], 0, 25))."'";
    $fields[] = "father_cell_phone='".mysql_escape_string(substr($_POST['father_cell_phone'], 0, 25))."'";

    $fields[] = "pin_code=".intval(substr($_POST['pin_code'], 0, 6))."";
    $fields[] = "email='".substr($_POST['email'], 0, 25)."'";
    $fields[] = "smsphone='".mysql_escape_string(substr($_POST['smsphone'], 0, 11))."'";


    db_query("INSERT students SET ".implode(', ', $fields));
    $student_id = db_get_insert_id();
    db_query("INSERT students_in_class VALUES ($class_id, $student_id, 0)");
    header('Location: student.php?mode=success_add&class_id='.$class_id);
    exit();
  } elseif ($action == 'update') {
    $fields = array();

    /* Информация об ученике*/
    $fields[] = "last_name='". mysql_escape_string(substr($_POST['last_name'], 0, 25))."'";
    $fields[] = "first_name='".mysql_escape_string(substr($_POST['first_name'], 0, 25))."'";
    $fields[] = "middle_name='".mysql_escape_string(substr($_POST['middle_name'], 0, 25))."'";
    $fields[] = "birthday='".mysql_escape_string(implode('-', array_reverse(explode('.', $_POST['birthday']))))."'";
    $fields[] = "address='".mysql_escape_string(substr($_POST['address'], 0, 255))."'";
    $fields[] = "phone='".mysql_escape_string(substr($_POST['phone'], 0, 25))."'";

    /* Информация о родителях*/
    $fields[] = "mother_fio='".mysql_escape_string(substr($_POST['mother_fio'], 0, 50))."'";
    $fields[] = "mother_work_phone='".mysql_escape_string(substr($_POST['mother_work_phone'], 0, 25))."'";
    $fields[] = "mother_cell_phone='".mysql_escape_string(substr($_POST['mother_cell_phone'], 0, 25))."'";

    $fields[] = "father_fio='".mysql_escape_string(substr($_POST['father_fio'], 0, 50))."'";
    $fields[] = "father_work_phone='".mysql_escape_string(substr($_POST['father_work_phone'], 0, 25))."'";
    $fields[] = "father_cell_phone='".mysql_escape_string(substr($_POST['father_cell_phone'], 0, 25))."'";
    $fields[] = "pin_code=".intval(substr($_POST['pin_code'], 0, 6))."";
    $fields[] = "email='".substr($_POST['email'], 0, 25)."'";
    $fields[] = "smsphone='".mysql_escape_string(substr($_POST['smsphone'], 0, 11))."'";

    db_query("UPDATE students SET ".implode(', ', $fields)." WHERE student_id=".$student_id);
    header('Location: student.php?mode=success_update');
    exit();
  }
}
  include('../header_dialog.php');

  if ($student_id == 0 && $mode == '') {
  	$mode = 'add';
  } elseif ($student_id != 0 && $mode == '') {
  	$mode = 'update';
  }

  if ($mode == 'success_update') {
  	echo '<center>Информация об ученике успешно обновлена.<br /><br />';
  	echo '<input type="button" value="&nbsp;&nbsp;Закрыть&nbsp;&nbsp;" onclick="self.parent.tb_remove();self.parent.location.reload();" />';
  } elseif ($mode == 'success_add') {
  	echo '<center>Новый ученик успешно добавлен.<br /><br />';
  	echo '<input type="button" value="&nbsp;&nbsp;Закрыть&nbsp;&nbsp;" onclick="self.parent.tb_remove();self.parent.location.reload();" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
  	<input type="button" value="&nbsp;&nbsp;Продолжить&nbsp;&nbsp;" onclick="document.location=\'student.php?mode=add&class_id='.$class_id.'\'" /></center>';
  } elseif ($mode == 'update') {
    $student = db_get_first_row('SELECT * FROM students WHERE student_id='.$student_id);
    outStudentForm($student);
  } elseif ($mode == 'add') {
    outStudentForm();
  }

function outStudentForm($student = null)
{
	global $class_id, $student_id;
	echo '


<script type="text/javascript">
	jQuery(function($){
	$.mask.definitions[\'~\']=\'[01]\';
	$.mask.definitions[\'a\']=\'[0123]\';
	$.mask.definitions[\'b\']=\'[12]\';
  $.mask.definitions[\'c\']=\'[09]\';
  $("#birthday_id").mask("a9.~9.bc99");
  $("#phone_id").mask("(999) 999-99-99");
  $("#mother_work_phone_id").mask("(999) 999-99-99");
  });
</script>

<script type="text/javascript">
$(document).ready(function() {
	$("#studentForm").validate();
});
</script>


<form action="student.php" method="post" id="studentForm">';
if (is_null($student)) {
  echo '<input type="hidden" name="action" value="add" />';
} else {
	echo '<input type="hidden" name="action" value="update" />';
}
echo '<input type="hidden" name="class_id" value="'.$class_id.'" />
<input type="hidden" name="student_id" value="'.$student['student_id'].'" />

<table width="100%" id="edit">
<tbody>
  <tr class="TableHead" valign="top" width="100%">
    <th width="35%">Данные ученика:</th>
    <th width="65%">Данные родителей:</th>
  </tr>
  <tr>
  <td rowspan="4">
<table id="edit_in">
<tbody>
  <tr>
    <td><label for="lname">Фамилия</label></td>
    <td><input type="text" name="last_name" class="required" value="'.(isset($student)?$student['last_name']:'').'" size="26" /></td>
  </tr>
  <tr>
    <td>Имя</td>
    <td><input type="text" name="first_name" class="required" value="'.(isset($student)?$student['first_name']:'').'" size="26" /></td>
  </tr>
  <tr>
    <td>Отчество</td>
    <td><input type="text" name="middle_name" class="required" value="'.(isset($student)?$student['middle_name']:'').'" size="26" /></td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td>Дата рождения</td>
    <td>
<!--script type="text/javascript">
	$(function() {
		$("#birthday_id").datepicker({
			changeMonth: true,
			changeYear: true,
			showButtonPanel: true,
			yearRange: \'1990:2010\'
		});
	});
	</script-->

    <input type="text" name="birthday" id="birthday_id" value="'.(isset($student)?implode('.', array_reverse(explode('-', $student['birthday']))):'').'" size="26" /></td>
  </tr>
  <tr>
    <td>Телефон</td>
    <td><input type="text" name="phone" id="phone_id" class="required" value="'.(isset($student['phone'])?$student['phone']:'').'" size="26" /></td>
  </tr>
  <tr>
    <td>Адрес</td>
    <td><textarea name="address" id="address_id" class="required" rows="4">'.(isset($student['address'])?$student['address']:'').'</textarea></td>
  </tr>
</tbody>
</table>
  </td>
  <th class="TableHead">Мать ребенка</th>
</tr>
<tr>
  <td>
<table id="edit_in" width="100%">
  <tr>
  <td>ФИО Матери</td>
  <td><input type="text" name="mother_fio" value="'.(isset($student['mother_fio'])?$student['mother_fio']:'').'" size="40" /></td>
  </tr>
  <tr>
    <td>Рабочий телефон</td>
    <td><input type="text" name="mother_work_phone" id="mother_work_phone_id" value="'.(isset($student['mother_work_phone'])?$student['mother_work_phone']:'').'" /></td>
  </tr>
  <tr>
    <td>Сотовый телефон</td>
    <td><input type="text" name="mother_cell_phone" value="'.(isset($student['mother_cell_phone'])?$student['mother_work_phone']:'').'" /></td>
  </tr>
</table>
  </td>
</tr>
<tr class="TableHead">
  <th>Отец ребенка</th>
</tr>
<tr>
  <td>
<table id="edit_in" width="100%">
  <tr>
    <td>ФИО Отца</td>
    <td><input type="text" name="father_fio" value="'.(isset($student['father_fio'])?$student['father_fio']:'').'" size="40" /></td>
  </tr>
  <tr>
    <td>Рабочий телефон</td>
    <td><input type="text" name="father_work_phone" value="'.(isset($student['father_work_phone'])?$student['mother_work_phone']:'').'" /></td>
  </tr>
  <tr>
    <td>Сотовый телефон</td>
    <td><input type="text" name="father_cell_phone" value="'.(isset($student['father_cell_phone'])?$student['mother_work_phone']:'').'" /></td>
  </tr>
</table>
  </td>
</tr>
<tr>
  <td colspan="2">

<table width="100%" id="edit_in">
  <tr>
    <td>PIN код</td>
    <td><input type="text" name="pin_code" size="7" maxlength="6" value="'.(is_null($student)?rand(100000, 999999):$student['pin_code']).'"></td>
    <td>E-Mail</td>
    <td><input type="text" name="email" class="email" value="'.(isset($student['email'])?$student['email']:'').'" size="40" ></td>
  </tr>
</table>

  </td>
</tr>
<tr>
  <td colspan="2">Телефон для SMS:&nbsp;<input type="text" name="smsphone"  value="'.(isset($student['smsphone'])?$student['smsphone']:'').'"></td>
</tr>
</tbody>
</table>
<center><br />';
if (is_null($student)) {
  echo '<input type="submit" class="button" value="Добавить" />';
} else {
	echo '<input type="submit" class="button" value="Обновить" />';
}
echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="button" class="button" value="&nbsp;&nbsp;Закрыть&nbsp;&nbsp;" onclick="self.parent.tb_remove();self.parent.location.reload();" />
</center>
</form>';
}

?>