<?php
/*****************************************************************************\
+-----------------------------------------------------------------------------+
| SchoolReg                                                                   |
| Copyright (c) 2009 Sergey V. Kuzin <sergey@kuzin.name>                      |
| All rights reserved.                                                        |
+-----------------------------------------------------------------------------+
\*****************************************************************************/

#
# $Id: teachers.php 4 2010-02-02 18:52:58Z kuzmich $
#

define('ADMIN_ZONE', true);

include_once ('../init.php');
include 'header.php';
include_once ('../include/teachers.php');
?>
<div align="center">
<span class="head_top">Список учителей школы:</span>
<table id="rounded-corner">
  <thead>
  <tr>
    <th class="rounded-left"> Ф.И.О.</th>
    <th class="rounded-right">&nbsp;</th>
  </tr>
  </thead>
  <tbody>
<?php
  $teacher_list = get_teachers_list();
  foreach($teacher_list as $teacher) {

  echo "<tr><td>$teacher[first_name] $teacher[middle_name] $teacher[last_name]</td><td>
  <a href=\"teacher.php?teacher_id=$teacher[teacher_id]&".uniqid('')."&keepThis=true&TB_iframe=true&height=350&width=330&modal=true\" class=\"thickbox\" title=\"Редактировать информацию об учителе\">Редактировать</a></td></tr>";

  }
?>
</tbody>
<tfoot>
    	<tr>
       	  <td class="rounded-foot-left">&nbsp;</td>
        	<td class="rounded-foot-right"><a href="" onClick="javascript: tb_show('Добавить', 'teacher.php?<?php echo uniqid('') ?>&keepThis=true&TB_iframe=true&height=330&width=350&modal=true'); return false;" class="add" title="Добавить">Добавить</a>
      </td>
        </tr>
    </tfoot>
</table>
</div>
<?php
include 'footer.php';
?>