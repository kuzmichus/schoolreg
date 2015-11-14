<?php
/*****************************************************************************\
+-----------------------------------------------------------------------------+
| SchoolReg                                                                   |
| Copyright (c) 2009 Sergey V. Kuzin <sergey@kuzin.name>                      |
| All rights reserved.                                                        |
+-----------------------------------------------------------------------------+
\*****************************************************************************/

#
# $Id: disciplines.php 4 2010-02-02 18:52:58Z kuzmich $
#


define('ADMIN_ZONE', true);

include_once ('../init.php');
include 'header.php';
include_once ('../include/teachers.php');
?>
<div align="center">
<span class="head_top">Список дисциплин школы:</span>
<table id="rounded-corner">
  <thead>
  <tr>
    <th class="rounded-left"> Название </th>
    <th class="rounded-right">&nbsp;</th>
  </tr>
  <thead>
  <tbody>
<?php
  $res = db_query('SELECT * FROM disciplines');
  while($row = mysql_fetch_assoc( $res)) {

  echo "<tr><td>$row[discipline]</td><td><a href=\"discipline.php?discipline_id=$row[discipline_id]&TB_iframe=true&height=110&width=450&".uniqid('r')."\" title=\"Редактировать дисциплину\" class=\"thickbox\">Редактировать</a></td></tr>";

  }
?>
</tbody>
<tfoot>
    	<tr>
       	  <td class="rounded-foot-left">&nbsp;</td>
        	<td class="rounded-foot-right"><a href="" onClick="javascript: tb_show('Добавить', 'discipline.php?TB_iframe=true&height=110&width=450&<?php echo uniqid('r'); ?>'); return false;" class="add" title="Добавить новую дисциплину">Добавить</a>
</td>
        </tr>
    </tfoot>
</table>
</div>
<?php
include 'footer.php';
?>