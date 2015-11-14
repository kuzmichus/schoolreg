<?php
#
# $Id: header.php 17 2010-03-30 16:45:00Z kuzmich $
#

?>
<html>
  <head>
    <title>Школьный журнал</title>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
    <script type="text/javascript" src="js/jquery.js"></script>
    <script type="text/javascript" src="js/jquery-ui.js"></script>
    <script type="text/javascript" src="js/i18n/jquery-ui.datepicker-ru.js"></script>
    <link rel="stylesheet" type="text/css" href="style.css" />
  </head>
  <body>
<?php if (isset($_SESSION['student_id']))  { ?>
<br />
<div align="center">
 <table border="0" cellspacing="0" cellpadding="0" class="table_menu" style="width:200px">
  <tr>
    <td><img src="images/circle_left_top.gif" alt="" width="6" height="6"></td>
    <td valign="top" class="border_top"><img src="images/border.gif" alt="" width="1" height="1"></td>
    <td><img src="images/circle_right_top.gif" alt="" width="6" height="6"></td>
  </tr>
  <tr>
    <td class="border_left">&nbsp;</td>
    <td class="padding"><table>
      <tr>
        <td nowrap="nowrap">&nbsp;<a href="index.php">Просмотр оценок</a>&nbsp;</td>
        <td align="center"><img src="../images/dec.png" alt="" width="1" height="51"></td>
        <td>&nbsp;<a href="index.php?action=logout">Выход</a>&nbsp;</td>
      </tr>
    </table></td>
    <td class="border_right">&nbsp;</td>
  </tr>
  <tr>
    <td><img src="images/circle_left_bottom.gif" alt="" width="6" height="6"></td>
    <td width="99%" valign="bottom" class="border_bottom"><img src="images/border.gif" alt="" width="1" height="1"></td>
    <td><img src="images/circle_right_bottom.gif" alt="" width="6" height="6"></td>
  </tr>
</table>
</div>

<?php
$student = get_student($_SESSION['student_id']);

echo "$student[last_name] $student[first_name] $student[middle_name]<br />";
?>
<?php } ?>