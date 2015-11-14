<?php
/*****************************************************************************\
+-----------------------------------------------------------------------------+
| SchoolReg                                                                   |
| Copyright (c) 2009 Sergey V. Kuzin <sergey@kuzin.name>                      |
| All rights reserved.                                                        |
+-----------------------------------------------------------------------------+
\*****************************************************************************/

#
# $Id: users.php 4 2010-02-02 18:52:58Z kuzmich $
#

define('ADMIN_ZONE', true);

include_once ('../init.php');
include 'header.php';
include_once ('../include/users.php');
?>
<div align="center">
<span class="head_top">Список пользователей:</span>
<table id="rounded-corner">
  <thead>
  <tr>
    <th class="rounded-left">Логин</th>
    <th> Ф.И.О.</th>
    <th class="rounded-right">&nbsp;</th>
  </tr>
  </thead>
  <tbody>
<?php
  $user_list = get_users_list();
  foreach($user_list as $user) {

  echo "<tr><td>$user[login]</td><td>$user[last_name] $user[first_name] $user[middle_name]</td><td>
  <a href=\"user.php?user_id=$user[user_id]&".uniqid('')."&keepThis=true&TB_iframe=true&height=400&width=600\" class=\"thickbox\" title=\"Редактировать информацию об учителе\">Редактировать</a></td></tr>";

  }
?>
</tbody>
<tfoot>
    	<tr>
       	  <td class="rounded-foot-left" colspan="2">&nbsp;</td>
        	<td class="rounded-foot-right"><a href="" onClick="javascript: tb_show('Добавить', 'user.php?<?php echo uniqid('') ?>&keepThis=true&TB_iframe=true&height=400&width=600'); return false;" class="add" title="Добавить учителя">Добавить</a>
</td>
        </tr>
    </tfoot>
</table>
</div>
<?php
include 'footer.php';
?>