<?php
/*****************************************************************************\
+-----------------------------------------------------------------------------+
| SchoolReg                                                                   |
| Copyright (c) 2009 Sergey V. Kuzin <sergey@kuzin.name>                      |
| All rights reserved.                                                        |
+-----------------------------------------------------------------------------+
\*****************************************************************************/


#
# $Id: user.php 4 2010-02-02 18:52:58Z kuzmich $
#

define('ADMIN_ZONE', true);

include_once ('../init.php');

$user_id   = @intval($_REQUEST['user_id']);
$mode       = @$_REQUEST['mode'];

if ($user_id == 0 && $mode == '') {
	$mode = 'add';
} elseif ($user_id != 0 && $mode == '') {
	$mode = 'update';
}



if (isset($_REQUEST['action'])) {
  $action = $_REQUEST['action'];

  if ($action == 'add') {
    $fields = array();
    $fields['login'] = substr($_POST['login'], 0, 25);
    $fields['passwd'] = md5($_POST['passwd']);
    $fields['last_name'] = substr($_POST['last_name'], 0, 25);
    $fields['first_name'] = substr($_POST['first_name'], 0, 25);
    $fields['middle_name'] = substr($_POST['middle_name'], 0, 25);
    $fields['access'] = intval($_POST['access']);

    db_array2insert('users', $fields);
    header('Location: student.php?mode=success_add');
    exit();
  } elseif ($action == 'update') {
    $fields = array();
    $fields['last_name'] = substr($_POST['last_name'], 0, 25);
    $fields['first_name'] = substr($_POST['first_name'], 0, 25);
    $fields['middle_name'] = substr($_POST['middle_name'], 0, 25);
    $fields['access'] = intval($_POST['access']);
    db_array2update('users', $fields,'user_id='.$user_id);
    header('Location: user.php?mode=success_update');
    exit();
  }
}
  include('../header_dialog.php');
?>
  <body>
<?php

  if ($mode == 'success_update') {
  	echo '<center>Информация о пользователе успешно обновлена.<br /><br />';
  	echo '<input type="button" value="&nbsp;&nbsp;Закрыть&nbsp;&nbsp;" onclick="self.parent.tb_remove();self.parent.location.reload();" />';
  } elseif ($mode == 'success_add') {
  	echo '<center>Новый пользователь успешно добавлен.<br /><br />';
  	echo '<input type="button" value="&nbsp;&nbsp;Закрыть&nbsp;&nbsp;" onclick="self.parent.tb_remove();self.parent.location.reload();" /></center>';
  } elseif ($mode == 'update') {
    $user = db_get_first_row('SELECT * FROM users WHERE user_id='.$user_id);
    outUserForm($user);
  } elseif ($mode == 'add') {
    outUserForm();
  }

function outUserForm($user = null)
{
	global $class_id, $student_id;
	echo '
<form action="user.php" method="post">';
if (is_null($user)) {
  echo '<input type="hidden" name="action" value="add" />';
} else {
	echo '<input type="hidden" name="action" value="update" />';
}
echo '<input type="hidden" name="user_id" value="'.$user['user_id'].'" />

<table>
  <tr>
    <td>Логин</td>
    <td><input type="text" name="login" value="'.(isset($user)?$user['login']:'').'"'.(isset($user)?' readonly="readonly"':'').' size="26" /></td>
  </tr>
  <tr>
    <td>Пароль</td>
    <td><input type="password" name="passwd" value="" size="26" /></td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td>Фамилия</td>
    <td><input type="text" name="last_name" value="'.(isset($user)?$user['last_name']:'').'" size="26" /></td>
  </tr>
  <tr>
    <td>Имя</td>
    <td><input type="text" name="first_name" value="'.(isset($user)?$user['first_name']:'').'" size="26" /></td>
  </tr>
  <tr>
    <td>Отчество</td>
    <td><input type="text" name="middle_name" value="'.(isset($user)?$user['middle_name']:'').'" size="26" /></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>Доступ</td>
    <td>
      <select name="access">
        <option value="1"'.((isset($user)&&$user['access']==1)?' selected="selected"':'').'>Администратор</option>
      </select>
  </tr>
</table>
<center>';
if (is_null($user)) {
  echo '<input type="submit" value="Добавить" />';
} else {
	echo '<input type="submit" value="Обновить" />';
}
echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="button" value="&nbsp;&nbsp;Закрыть&nbsp;&nbsp;" onclick="self.parent.tb_remove();self.parent.location.reload();" />
</center>
</form>';
}

?>
  </body>
</html>