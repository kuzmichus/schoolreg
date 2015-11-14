<?php
/*****************************************************************************\
+-----------------------------------------------------------------------------+
| SchoolReg                                                                   |
| Copyright (c) 2009 Sergey V. Kuzin <sergey@kuzin.name>                      |
| All rights reserved.                                                        |
+-----------------------------------------------------------------------------+
\*****************************************************************************/

#
# $Id: services.php 19 2010-04-04 08:57:11Z kuzmich $
#

define('ADMIN_ZONE', true);

include_once ('../init.php');

$smssend  = false;

if (isset($_REQUEST['action']) && $_REQUEST['action']=='sendgrade') {

  $headers = 'MIME-Version: 1.0' . "\r\n";
  $headers .= 'Content-Type: text/plain; charset=UTF-8' . "\r\n";

	$res = db_query($sql = "SELECT student_id FROM students_on_lesson INNER JOIN lessons ON lessons.lesson_id=students_on_lesson.lesson_id WHERE lessons.lesson_date='".implode('-', array_reverse(explode('.', $_REQUEST['date_send'])))."' GROUP BY student_id");
	while ($student_id = mysql_fetch_assoc($res)) {
		$student_id = array_pop($student_id);

		$student = db_get_first_row("SELECT * FROM students WHERE student_id=$student_id;");
		$text = "Ученик $student[last_name] $student[last_name] $student[last_name] за ".$_REQUEST['date_send']." получил слудующие оценки:\n";
		$res2 = db_query("SELECT grade, discipline
FROM students_on_lesson
INNER JOIN lessons ON lessons.lesson_id = students_on_lesson.lesson_id
INNER JOIN subjects ON lessons.subject_id = subjects.subject_id
INNER JOIN disciplines ON disciplines.discipline_id = subjects.discipline_id
WHERE lessons.lesson_date = '".implode('-', array_reverse(explode('.', $_REQUEST['date_send'])))."' AND student_id=$student_id;");
  while($grade = mysql_fetch_assoc($res2)) {
    $text .="$grade[discipline]: $grade[grade]\n";
  }
  if ($student['email'] != '') {
  	mail($student['email'], "Оценки за $student[last_name] $student[last_name] $student[last_name] за ".$_REQUEST['date_send'], $text, $headers);
  }
	}

}

if (isset($_REQUEST['action']) && $_REQUEST['action']=='sendtosms') {

  require_once('../lib/epochtasms.Class.php');
  $epochtasms = new epochtasms();

	$res = db_query($sql = "SELECT student_id FROM students_on_lesson INNER JOIN lessons ON lessons.lesson_id=students_on_lesson.lesson_id WHERE lessons.lesson_date='".implode('-', array_reverse(explode('.', $_REQUEST['date_send'])))."' GROUP BY student_id");
	while ($student_id = mysql_fetch_assoc($res)) {
		$student_id = array_pop($student_id);

		$student = db_get_first_row("SELECT * FROM students WHERE student_id=$student_id;");
		$text = "Оценки $student[last_name] $student[last_name] $student[last_name] за ".$_REQUEST['date_send'].":";
		$res2 = db_query("SELECT grade, discipline
FROM students_on_lesson
INNER JOIN lessons ON lessons.lesson_id = students_on_lesson.lesson_id
INNER JOIN subjects ON lessons.subject_id = subjects.subject_id
INNER JOIN disciplines ON disciplines.discipline_id = subjects.discipline_id
WHERE lessons.lesson_date = '".implode('-', array_reverse(explode('.', $_REQUEST['date_send'])))."' AND student_id=$student_id;");
  while($grade = mysql_fetch_assoc($res2)) {
    $text .="$grade[discipline]: $grade[grade], ";
  }
  if ($student['smsphone'] != '') {
    $text = htmlspecialchars($text);
    $result = $epochtasms->SendTextMessage($config['epochtasms']['login']
                                          ,$config['epochtasms']['passwd']
                                          ,$student['smsphone']
                                          ,$text
                                          ,$config['epochtasms']['from']);
  }
	}
  $smssend = true;
}


include 'header.php';
$nums_grade = db_get_cell("SELECT COUNT(grade) FROM students_on_lesson INNER JOIN lessons ON lessons.lesson_id=students_on_lesson.lesson_id WHERE lessons.lesson_date='".date('Y-m-d')."';");
?>
<table id="edit">
  <tbody>
  <tr class="TableHead">
    <th colsapan="2">Отсылка оценок родителям на E-Mail</th>
  </tr>
  <!--tr>
    <td>За <?php echo date('d.m.Y'); ?> выставленно <?php echo $nums_grade ?> оценок.</td>
  </tr-->
  <tr>
	 <td>
	 <form action="services.php" method="post">
	 <input type="hidden" name="action" value="sendgrade" />

    Выслать оценки за <input type="text" id="date_send_id" name="date_send" value="<?php echo date('d.m.Y'); ?>" /> родителям
    <input type="submit"  class="button" value=" Выслать " />
<script type="text/javascript">
	$(function() {
		$('#date_send_id').datepicker({
			changeMonth: true,
			changeYear: true,
			showButtonPanel: true
		});
	});
	</script>
    </form>
    </td>
  <tr>
  </tbody>
</table>
<br /><br />
<table id="edit">
  <tbody>
  <tr class="TableHead">
    <th colsapan="2">Отсылка оценок родителям на телефон</th>
  </tr>
<?php if ($smssend)  {?>
<tr><td>Оценку отправлены.</td></tr>
<?php } ?>
  <!--tr>
    <td>За <?php echo date('d.m.Y'); ?> выставленно <?php echo $nums_grade ?> оценок.</td>
  </tr-->
  <tr>
	 <td>
	 <form action="services.php" method="post">
	 <input type="hidden" name="action" value="sendtosms" />

    Выслать оценки за <input type="text" id="date_sendsms_id" name="date_send" value="<?php echo date('d.m.Y'); ?>" /> родителям
    <input type="submit"  class="button" value=" Выслать " />
<script type="text/javascript">
	$(function() {
		$('#date_sendsms_id').datepicker({
			changeMonth: true,
			changeYear: true,
			showButtonPanel: true
		});
	});
	</script>
    </form>
    </td>
  <tr>
  </tbody>
</table>
<?php
include 'footer.php';
?>