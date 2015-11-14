<?php
/*****************************************************************************\
+-----------------------------------------------------------------------------+
| SchoolReg                                                                   |
| Copyright (c) 2009 Sergey V. Kuzin <sergey@kuzin.name>                      |
| All rights reserved.                                                        |
+-----------------------------------------------------------------------------+
\*****************************************************************************/

#
# $Id: subject.php 4 2010-02-02 18:52:58Z kuzmich $
#

define('ADMIN_ZONE', true);

include_once ('../init.php');
include_once ('../include/classes.php');

$class_id    = @intval($_REQUEST['class_id']);
$subject_id  = @intval($_REQUEST['subject_id']);
$mode        = @$_REQUEST['mode'];



if (isset($_REQUEST['action'])) {
  $action = $_REQUEST['action'];

  if ($action == 'add') {

    $fields[] = "class_id=". intval($_POST['class_id']);
    $fields[] = "teacher_id=". intval($_POST['teacher_id']);
    $fields[] = "discipline_id=". intval($_POST['discipline_id']);

    db_query($sql = "INSERT subjects SET ".implode(', ', $fields));
    header('Location: subject.php?mode=success_add');
    exit();

  } elseif ($action == 'update') {
    $fields = array();

    $fields[] = "teacher_id=". intval($_POST['teacher_id']);
    $fields[] = "discipline_id=". intval($_POST['discipline_id']);


    db_query($sql = "UPDATE subjects SET ".implode(', ', $fields).' WHERE subject_id='.$subject_id);
    header('Location: subject.php?mode=success_update');
    exit();

  }
}
  include('../header_dialog.php');
?>
  <body>
<?php
  if ($subject_id == 0 && $mode == '') {
  	$mode = 'add';
  } elseif ($subject_id != 0 && $mode == '') {
  	$mode = 'update';
  }

  if ($mode == 'success_update') {
  	echo '<center>Информация об ученике успешно обновлена.<br /><br />';
  	echo '<input type="button" value="&nbsp;&nbsp;Закрыть&nbsp;&nbsp;" onclick="self.parent.tb_remove();self.parent.location.reload();" /></center>';
  } elseif ($mode == 'success_add') {
  	echo 'Новый дисциплина успешно добавлена.<br /><br />';
  	echo '<input type="button" value="&nbsp;&nbsp;Закрыть&nbsp;&nbsp;" onclick="self.parent.tb_remove();self.parent.location.reload();" />';
  } elseif ($mode == 'update') {
    $subject  = db_get_first_row('SELECT * FROM subjects WHERE subject_id='.$subject_id);
    outSubjectForm($subject);
  } elseif ($mode == 'add') {
    outSubjectForm();
  }

function outSubjectForm($subject = null)
{
	global $class_id, $subject_id;
	$res = db_query('SELECT * FROM disciplines');
	$disciplines = array();
	while($row = mysql_fetch_array($res)) {
    $disciplines[] = $row;
	}

	$res = db_query('SELECT * FROM teachers');
	$teachers = array();
	while($row = mysql_fetch_array($res)) {
    $teachers[] = $row;
	}

	echo '
<form action="subject.php" method="post">';
if (isset($subject)) {
  echo '<input type="hidden" name="action" value="update" />';
} else {
  echo '<input type="hidden" name="action" value="add" />';
}
echo '
<input type="hidden" name="class_id" value="'.$class_id.'" />
<input type="hidden" name="subject_id" value="'.$subject_id.'" />
<table width="100%"
  <tr>
    <td>Дисциплина</td>
    <td>
<select name="discipline_id">';
foreach($disciplines as $discipline) {
  echo '<option value="'.$discipline['discipline_id'].'"';
  if ($discipline['discipline_id'] == $subject['discipline_id']) {
  	echo ' selected="selected"';
  }
  echo '>'.$discipline['discipline'].'</option>';
}
echo '</select>
    </td>
  </tr>
  <tr>
    <td>Учитель</td>
    <td>
<select name="teacher_id">';
foreach($teachers as $teacher) {
  echo '<option value="'.$teacher['teacher_id'].'"';
  if ($teacher['teacher_id'] == $subject['teacher_id']) {
  	echo ' selected="selected"';
  }
  echo '>'.$teacher['last_name'].' '.$teacher['first_name'].' '.$teacher['middle_name'].'</option>';
}
echo '</select>
    </td>
  </tr>
  <tr><td colspan="2">&nbsp;</td></tr>
  <tr>
    <td colspan="2" align="center">';
if (isset($subject)) {
	echo '<input type="submit" value="Сохранить">';
} else {
  echo '<input type="submit" value="Добавить">';
}
echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="button" value="&nbsp;&nbsp;Закрыть&nbsp;&nbsp;" onclick="self.parent.tb_remove();self.parent.location.reload();" /></td>
</tr>
</table>
</form>';
}

?>
  </body>
</html>