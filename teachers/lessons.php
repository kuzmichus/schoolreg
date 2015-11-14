<?php
/*****************************************************************************\
+-----------------------------------------------------------------------------+
| SchoolReg                                                                   |
| Copyright (c) 2009 Sergey V. Kuzin <sergey@kuzin.name>                      |
| All rights reserved.                                                        |
+-----------------------------------------------------------------------------+
\*****************************************************************************/

#
# $Id: lessons.php 4 2010-02-02 18:52:58Z kuzmich $
#

define('TEACHER_ZONE', true);
include_once ('../init.php');
include_once ('../include/classes.php');

$subject_id = intval($_REQUEST['subject_id']);

db_query('UPDATE lessons SET active = 0 WHERE active<'.time());

if(isset($_POST['action'])) {
  $action = $_POST['action'];
  if ($action == 'update') {
  	$grades = $_POST['grades'];
  	foreach ($grades as $lesson_id => $lesson) {
      db_query($sql = 'DELETE FROM students_on_lesson WHERE lesson_id='.$lesson_id);
      foreach ($lesson as $student_id => $grade) {
      	if ($grade != '') {
        	db_query('INSERT INTO students_on_lesson VALUES('.$student_id.', '.$lesson_id.", '".substr($grade, 0, 2)."')");
      	}
      }
  	}
  	header ('Location: lessons.php?subject_id='.$subject_id);
    exit();
  } elseif ($action == 'close') {
  	$grades = $_POST['grades'];
  	foreach ($grades as $lesson_id => $lesson) {
      db_query($sql = 'DELETE FROM students_on_lesson WHERE lesson_id='.$lesson_id);
      foreach ($lesson as $student_id => $grade) {
      	if ($grade != '') {
        	db_query('INSERT INTO students_on_lesson VALUES('.$student_id.', '.$lesson_id.", '".substr($grade, 0, 2)."')");
      	}
      }
  	}
  	db_query('UPDATE lessons SET active=0 WHERE lesson_id='.$lesson_id);
  	header ('Location: lessons.php?subject_id='.$subject_id);
    exit();
  }
}

include 'header.php';
?>
<br />
<div>
<span class="head_top">Список классов:</span>

<a href="new_lesson.php?subject_id=<?php echo $subject_id ?>&TB_iframe=true&height=300&width=400&<?php echo uniqid('r'); ?>" title="Создать новый урок" class="thickbox">Новый урок</a>


<script type="text/javascript">
$(function() {
$(".grade").editable("savegrade.php", {
      indicator : "<img src='images/indicator.gif'>",
      tooltip   : "Двойной клик для редактирования...",
      placeholder: '&nbsp;&nbsp;&nbsp;',
      event     : "dblclick",
      width : 25
  });
});
</script>

<table width="100%" border="0" ><tr><td width="50%" valign="top">
<form action="lessons.php?" method="post">
<input type="hidden" name="subject_id" value="<?php echo $subject_id ?>" />
<input type="hidden" name="action" value="update" />
<table  id="rounded-corner" width="100%" align="center">
  <thead>
  <tr class="TableHead">
    <th class="rounded-left">№</th>
    <th>Имя</th>
<?php
  $res = db_query('SELECT * FROM lessons WHERE subject_id='.$subject_id.' ORDER BY lesson_date');
  $lessons = array();
  $num_row = mysql_num_rows($res);
  $col_row = 0;
  while ($row = mysql_fetch_assoc($res)) {
  	$lessons[] = $row;
  	list($year, $month, $day) = explode('-', $row['lesson_date']);
  	$col_row++;
  	echo '<th style="writing-mode:tb-rl;"';
  	if ($col_row == $num_row) {
  		echo ' class="rounded-right"';
  	}
  	echo ">$day.$month</th>";
  }
?>
  </tr>
  </thead>
  <tbody>
<?php
  $active_leson = false;
  $subject = db_get_first_row('SELECT * FROM subjects WHERE subject_id='.$subject_id);
  $students_list = get_student_classes_list($subject['class_id']);
  $n=1;
  foreach($students_list as $student) {

  echo "<tr><td>$n</td><td nowrap=\"nowrap\">$student[student_name]</td>";
  $grades = get_grade_from_lesson($student['student_id'], $subject_id);
  foreach ($lessons as $lesson){
  	if ($grades[$lesson['lesson_id']] != '') {
  		if ($lesson['active'] == 0) {
      	echo '<td align="center"><span id="grade_'.$student['student_id'].'_'.$subject_id.'_'.$lesson['lesson_id'].'" class="grade" style="display: inline">'.$grades[$lesson['lesson_id']].'</span>';
      	echo '</td>'."\n";
      } else {
      	echo '<td><input type="text" name="grades['.$lesson['lesson_id'].']['.$student['student_id'].']" size="3" value="'.$grades[$lesson['lesson_id']].'" maxlength="2" /></td>';
      }
    } else {
    	if ($lesson['active'] == 0) {
      	echo '<td><span id="grade_'.$student['student_id'].'_'.$subject_id.'_'.$lesson['lesson_id'].'" class="grade" style="display: inline">&nbsp;&nbsp;&nbsp;&nbsp;</span>';
      	echo '</td>'."\n";
      } else {
      	$active_leson = true;
      	echo '<td><input type="text" name="grades['.$lesson['lesson_id'].']['.$student['student_id'].']" size="3" value="" maxlength="2" /></td>';
      }
    }
  }
  echo '</tr>';
  $n++;
  }
?>
  </tbody>
<tfoot>
    	<tr>
       	  <td class="rounded-foot-left">&nbsp;</td>
        	<td>&nbsp;</td>
<?php
for($i=1; $i<=$num_row; $i++) {
	echo '<td';
  	if ($i == $num_row) {
  		echo ' class="rounded-foot-right"';
  	}
  	echo ">&nbsp;</td>";
}
?>
        </tr>
    </tfoot>
</table>
<br />
<input type="submit" value="Сохранить"<?php if (!$active_leson ) echo ' disabled="disabled"' ?> />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="button" value="Закончить урок" onClick="javascript:this.form.action.value='close'; this.form.submit();"<?php if (!$active_leson ) echo ' disabled="disabled"' ?> />
</form>
</td>
<td width="1px" valign="top">&nbsp;</td>
<td width="50%" valign="top">
<table id="rounded-corner" width="100%" align="center">
<thead>
  <tr>
    <th class="rounded-left">Дата</th>
    <th width="86%">Тема урока</th>
    <th class="rounded-right">Дата</th>
  </tr>
  </thead>
  <tbody>
<?php
  $show_line = false;
  foreach($lessons as $lesson) {
  	list($year, $month, $day) = explode('-', $lesson['lesson_date']);
  	echo '<tr><td width="8%">'.$day.'.'.$month.'</td><td width="92%">'.$lesson['topic'].'</td><td></td></tr>';
  	$show_line = !$show_line;
  }
?>
  </tbody>
<tfoot>
    	<tr>
       	  <td class="rounded-foot-left">&nbsp;</td>
       	  <td>&nbsp;</td>
        	<td class="rounded-foot-right">&nbsp;</td>
        </tr>
    </tfoot>
</table>
</td></tr></table>
</div>
<?php

include 'footer.php';
?>