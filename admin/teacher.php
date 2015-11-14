<?php
/*****************************************************************************\
+-----------------------------------------------------------------------------+
| SchoolReg                                                                   |
| Copyright (c) 2009 Sergey V. Kuzin <sergey@kuzin.name>                      |
| All rights reserved.                                                        |
+-----------------------------------------------------------------------------+
\*****************************************************************************/

#
# $Id: teacher.php 4 2010-02-02 18:52:58Z kuzmich $
#

define('ADMIN_ZONE', true);

include_once ('../init.php');
include_once ('../include/classes.php');

$teacher_id    = @intval($_REQUEST['teacher_id']);
$mode        = @$_REQUEST['mode'];

if ($teacher_id == 0 && $mode == '') {
	$mode = 'add';
} elseif ($teacher_id != 0 && $mode == '') {
	$mode = 'update';
}



if (isset($_REQUEST['action'])) {
  $action = $_REQUEST['action'];

  if ($action == 'add') {

    $fields[] = "last_name='". mysql_escape_string(substr($_POST['last_name'], 0, 25))."'";
    $fields[] = "first_name='".mysql_escape_string(substr($_POST['first_name'], 0, 25))."'";
    $fields[] = "middle_name='".mysql_escape_string(substr($_POST['middle_name'], 0, 25))."'";

    $fields[] = "login='".mysql_escape_string(substr($_POST['login'], 0, 25))."'";
    $fields[] = "passwd='".md5($_POST['passwd'])."'";

    db_query($sql = "INSERT teachers SET ".implode(', ', $fields));
    header('Location: teacher.php?mode=success_add');
    exit();

  } elseif ($action == 'update') {
    $fields = array();

    $fields[] = "last_name='". mysql_escape_string(substr($_POST['last_name'], 0, 25))."'";
    $fields[] = "first_name='".mysql_escape_string(substr($_POST['first_name'], 0, 25))."'";
    $fields[] = "middle_name='".mysql_escape_string(substr($_POST['middle_name'], 0, 25))."'";

    $fields[] = "login='".mysql_escape_string(substr($_POST['login'], 0, 25))."'";

    if (isset($_POST['passwd'])) {
      $fields[] = "passwd='".md5($_POST['passwd'])."'";
    }

    db_query($sql = "UPDATE teachers SET ".implode(', ', $fields).' WHERE teacher_id='.$teacher_id);
    header('Location: teacher.php?mode=success_update');
    exit();

  }
}
  include('../header_dialog.php');
?>
  <script type="text/javascript">
  function checkValidForm() {
    var validForm = true;

    if (document.getElementById('last_name_id').value == '') {
    	validForm = false;
    	alert('Введите фамилию учителя');
    }

    if (document.getElementById('first_name_id').value == '') {
    	validForm = false;
    	alert('Введите имя учителя');
    }

    if (document.getElementById('middle_name_id').value == '') {
    	validForm = false;
    	alert('Введите отчество учителя');
    }

    if (document.getElementById('login_id').value == '') {
    	validForm = false;
    	alert('Введите логин учителя');
    }


    return validForm;
  }
  </script>
  <body style="margin-left: 0px;	margin-right: 0px;">
<?php

  if ($mode == 'success_update') {
  	echo '<center>Информация об учителе успешно обновлена.<br /><br />';
  	echo '<input type="button" value="&nbsp;&nbsp;Закрыть&nbsp;&nbsp;" onclick="self.parent.tb_remove();self.parent.location.reload();" /></center>';
  } elseif ($mode == 'success_add') {
  	echo '<center>Новый учитель успешно добавлен.<br /><br />';
  	echo '<input type="button" value="&nbsp;&nbsp;Закрыть&nbsp;&nbsp;" onclick="self.parent.tb_remove();self.parent.location.reload();" />
  	&nbsp;&nbsp;<input type="button" value="&nbsp;&nbsp;Продолжить&nbsp;&nbsp;" onclick="document.location=\'teacher.php\'" /></center>';
  } elseif ($mode == 'update') {
    $teacher  = db_get_first_row('SELECT * FROM teachers WHERE teacher_id='.$teacher_id);
    outTeacherForm($teacher);
  } elseif ($mode == 'add') {
    outTeacherForm();
  }

function outTeacherForm($teacher = null)
{
	global $teacher_id;
	echo '
<form action="teacher.php" method="post">';
if (isset($teacher)) {
  echo '<input type="hidden" name="action" value="update" />';
  echo '<input type="hidden" name="teacher_id" value="'.$teacher_id.'" />';
} else {
  echo '<input type="hidden" name="action" value="add" />';
}
echo '
<table width="100%" id="edit_in">
<tbody>
  <tr>
    <td>Фамилия<font color="red">*</font></td>
    <td><input type="text" name="last_name" id="last_name_id" value="'.(isset($teacher)?$teacher['last_name']:'').'" /></td>
  </tr>
  <tr>
    <td>Имя<font color="red">*</font></td>
    <td><input type="text" name="first_name" id="first_name_id" value="'.(isset($teacher)?$teacher['first_name']:'').'" /></td>
  </tr>
  <tr>
    <td>Отчество<font color="red">*</font></td>
    <td><input type="text" name="middle_name" id="middle_name_id" value="'.(isset($teacher)?$teacher['middle_name']:'').'" /></td>
  </tr>

  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td>Логин<font color="red">*</font></td>
    <td><input type="text" name="login" id="login_id" value="'.(isset($teacher)?$teacher['login']:'').'" /></td>
  </tr>
  <tr>
    <td>Пароль</td>
    <td><input type="password" name="passwd" value="" /></td>
  </tr>
  <tr>
    <td>Пароль</td>
    <td><input type="password" name="passwd2" value="" /></td>
  </tr>

  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>

  <tr>
    <td colspan="2" align="center">';
if (isset($teacher)) {
	echo '<input type="button" class="button" value="Сохранить" onClick="javascript: if (checkValidForm()) this.form.submit();">';
} else {
  echo '<input type="button" class="button" value="Добавить" onClick="javascript: if (checkValidForm()) this.form.submit();">';
}
echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="button" class="button" value="&nbsp;&nbsp;Закрыть&nbsp;&nbsp;" onclick="self.parent.tb_remove();self.parent.location.reload();" /></td>
</tr></tbody></table></form>';
}

?>
  </body>
</html>