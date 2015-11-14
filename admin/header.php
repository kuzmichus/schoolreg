<?php
#
# $Id: header.php 13 2010-03-28 22:28:34Z kuzmich $
#
?>
<html>
  <head>
    <title>Школьный журнал</title>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
    <link type="text/css" rel="stylesheet" href="../ui-theme/<?php echo $config['ui']['theme']?>/jquery-ui.css" />
    <link type="text/css" rel="stylesheet" href="../thickbox.css" media="screen" />
    <link type="text/css" rel="stylesheet" href="../style.css" />
    <script type="text/javascript" src="../js/jquery.js"></script>
    <script type="text/javascript" src="../js/jquery-ui.js"></script>
    <script type="text/javascript" src="../js/i18n/jquery-ui.datepicker-ru.js"></script>
    <script type="text/javascript" src="../js/jquery.validate.js"></script>
    <script type="text/javascript" src="../js/i18n/jquery.validate-ru.js"></script>
    <script type="text/javascript" src="../js/jquery.maskedinput.js"></script>
    <script type="text/javascript" src="../js/thickbox.js"></script>
  </head>
  <body>
<?php
if (isset($_SESSION['admin_id'])) {
?>
<br />
<div align="center">
 <table border="0" cellspacing="0" cellpadding="0" class="table_menu">
  <tr>
    <td><img src="../images/circle_left_top.gif" alt="" width="6" height="6"></td>
    <td valign="top" class="border_top"><img src="../images/border.gif" alt="" width="1" height="1"></td>
    <td><img src="../images/circle_right_top.gif" alt="" width="6" height="6"></td>
  </tr>
  <tr>
    <td class="border_left">&nbsp;</td>
    <td class="padding"><table width="100%">
      <tr>
        <td><a href="index.php">&nbsp;Главная&nbsp;</a></td>
        <td align="center"><img src="../images/dec.png" alt="" width="1" height="51"></td>
        <td><a href="classes.php">&nbsp;Классы&nbsp;</a></td>
        <td align="center"><img src="../images/dec.png" alt="" width="1" height="51"></td>
        <td><a href="teachers.php">&nbsp;Учителя&nbsp;</a></td>
        <td align="center"><img src="../images/dec.png" alt="" width="1" height="51"></td>
        <td><a href="disciplines.php">&nbsp;Дисциплины&nbsp;</a></td>
        <td align="center"><img src="../images/dec.png" alt="" width="1" height="51"></td>
        <td><a href="users.php">&nbsp;Пользователи&nbsp;</a></td>
        <td align="center"><img src="../images/dec.png" alt="" width="1" height="51"></td>
        <td nowrap="nowrap">&nbsp;<a href="curriculum.php">Учебный план</a>&nbsp;</td>
        <td align="center"><img src="../images/dec.png" alt="" width="1" height="51"></td>
        <td nowrap="nowrap">&nbsp;<a href="services.php">Сервисные операции</a>&nbsp;</td>
        <td align="center"><img src="../images/dec.png" alt="" width="1" height="51"></td>
        <td>&nbsp;<a href="login.php?action=logout">Выход</a>&nbsp;</td>
      </tr>
    </table></td>
    <td class="border_right">&nbsp;</td>
  </tr>
  <tr>
    <td><img src="../images/circle_left_bottom.gif" alt="" width="6" height="6"></td>
    <td width="99%" valign="bottom" class="border_bottom"><img src="../images/border.gif" alt="" width="1" height="1"></td>
    <td><img src="../images/circle_right_bottom.gif" alt="" width="6" height="6"></td>
  </tr>
</table>
</div>
<?php
}
?>
