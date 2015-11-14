<?php
/*****************************************************************************\
+-----------------------------------------------------------------------------+
| SchoolReg                                                                   |
| Copyright (c) 2009 Sergey V. Kuzin <sergey@kuzin.name>                      |
| All rights reserved.                                                        |
+-----------------------------------------------------------------------------+
\*****************************************************************************/

#
# $Id: class.php 19 2010-04-04 08:57:11Z kuzmich $
#

define('ADMIN_ZONE', true);

include_once ('../init.php');
include 'header.php';
include_once ('../include/classes.php');

?>
<div id="dialog" style="display:none"></div>
<div id="editStudentDialog" style="display:none">
<div id="tabs">
	<ul>
		<li><a href="#tabs-1">Ученик</a></li>
		<li><a href="#tabs-2">Родители</a></li>
		<li><a href="#tabs-3">Действия</a></li>
		<li><a href="#tabs-4">История</a></li>
	</ul>

<div id="tabs-1">
<table id="edit_in">
<tbody>
  <tr>
    <td><label for="lname">Фамилия</label></td>
    <td><input type="text" id="last_name" name="last_name" class="required" value="" size="26" /></td>

    <td>&nbsp;</td>
    <td>Дата рождения</td>
    <td>
      <input type="text" name="birthday" id="birthday_id" value="" size="26" />
    </td>
  </tr>
  <tr>
    <td>Имя</td>
    <td><input type="text" id="first_name" name="first_name" class="required" value="" size="26" /></td>
    <td>&nbsp;</td>
    <td>Телефон</td>
    <td><input type="text" name="phone" id="phone_id" class="required" value="" size="26" /></td>
  </tr>
  <tr>
    <td>Отчество</td>
    <td><input type="text" id="middle_name" name="middle_name" class="required" value="" size="26" /></td>
    <td>&nbsp;</td>
    <td>Адрес</td>
    <td><textarea name="address" id="address_id" class="required" rows="4"></textarea></td>
  </tr>
</tbody>
</table>
</div>
<div id="tabs-2"></div>
<div id="tabs-3"></div>
<div id="tabs-4"></div>
</div>
</div>
	<script type="text/javascript">
	$(function() {
		$("#tabs").tabs();
		$("#tabs").tabs( { disabled: [1, 2, 3] } );
	});

	function editStudent(student_id) {
	  $.getJSON("student.php", { action: "student", student_id: student_id }, function(student){
      //alert("JSON Data: " + student.first_name);

   		$("#tabs").tabs( { disabled: [2, 3] } );

      $('#last_name').val(student.last_name);
      $('#first_name').val(student.first_name);
      $('#middle_name').val(student.middle_name);
      $('#editStudentDialog').dialog({
	title:'Редактирование',
	modal:true,
	height:470,
	width:700,
buttons: {'Закрыть': function() {
	        $(this).dialog('close');
	      }}})
    });

	}
	</script>

<?php

$class_id = $_REQUEST['class_id'];

$class   = db_get_first_row('SELECT * FROM classes WHERE class_id='.$class_id);
$teacher = db_get_first_row('SELECT * FROM teachers WHERE teacher_id='.$class['teacher_id']);
?>

Класс <b><?php echo "$class[class]$class[letter]"; ?></b> Классный руководитель: <b><?php echo "$teacher[last_name] $teacher[first_name] $teacher[middle_name]";?></b><br />
<?php echo $class['school_year'].'-'.($class['school_year'] + 1)?> год обучения<br />

<table width="100%">
<tr><td width="50%" valign="top">

<span class="head_top">Список учеников:</span>
<table id="rounded-corner">
<thead>
  <tr>
    <th class="rounded-left">№</th>
    <th>Имя</th>
    <th>&nbsp;</th>
    <th class="rounded-right">&nbsp;</th>
  </tr>
  </thead>
<?php
  $students_list = get_student_classes_list($_GET['class_id']);
  $n=1;
  foreach($students_list as $student) {

  echo "<tr><td>$n</td><td>$student[student_name]</td><td>
  <a href=\"student.php?class_id=$_GET[class_id]&student_id=$student[student_id]&".uniqid('')."&keepThis=true&TB_iframe=true&height=450&width=770&modal=true\" title=\"Редактирвание ученика\" class=\"thickbox\">Редактировать</a></td>";
  echo "<td><a href=\"class.php?class_id=$_GET[class_id]&student_id=$student[student_id]&action=expel\" title=\"Отчислить ученика\">Отчислить</a></td></tr>";
  $n++;
  }
?>
      <tfoot>
    	<tr>
       	  <td colspan="2" class="rounded-foot-left">&nbsp;</td>
        	<td>
        	  <a href="" onClick="javascript: tb_show('Добавить', 'student.php?class_id=<?php echo $_GET['class_id'];?>&<?php echo uniqid(''); ?>&TB_iframe=true&height=450&width=770'); return false;" class="add" title="Добавить">Добавить</a>
          </td>
          <td class="rounded-foot-right">&nbsp;</td>
        </tr>
    </tfoot>
</table>
</td><td width="50%" valign="top">
<span class="head_top">Список преподаваемых дисциплин</span>
<table id="rounded-corner">
<thead>
  <tr>
    <th class="rounded-left">Дисциплина</th>
    <th>Учитель</th>
    <th class="rounded-right">&nbsp;</th>
  </tr>
  </thead>
<?php
  $res = db_query('SELECT subject_id, CONCAT_WS( \' \', teachers.last_name, teachers.first_name, teachers.middle_name ) AS name, discipline
FROM classes
LEFT JOIN subjects ON classes.class_id = subjects.class_id
LEFT JOIN disciplines ON subjects.discipline_id = disciplines.discipline_id
LEFT JOIN teachers ON teachers.teacher_id = subjects.teacher_id
WHERE classes.class_id='.$class_id.' ORDER BY discipline');
while($row = mysql_fetch_assoc($res)) {
  echo "<tr><td>$row[discipline]</td><td>$row[name]</td><td>";
  echo '<a href="subject.php?class_id='.$class_id.'&subject_id='.$row['subject_id'].'&TB_iframe=true&height=150&width=380" title="Изменить" class="thickbox">Изменить</a>';
  echo "</td></tr>";
}
?>
      <tfoot>
    	<tr>
       	  <td colspan="2" class="rounded-foot-left">&nbsp;</td>
        	<td class="rounded-foot-right">
        	  <a href="" onClick="javascript: tb_show('Добавить', 'subject.php?class_id=<?php echo $class_id?>&TB_iframe=true&height=150&width=380'); return false;" class="add" title="Добавить">Добавить</a>
          </td>
        </tr>
    </tfoot>
</table>
</td>
</tr></table>
<?php
include 'footer.php';
?>