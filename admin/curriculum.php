<?php
/*****************************************************************************\
+-----------------------------------------------------------------------------+
| SchoolReg                                                                   |
| Copyright (c) 2009 Sergey V. Kuzin <sergey@kuzin.name>                      |
| All rights reserved.                                                        |
+-----------------------------------------------------------------------------+
\*****************************************************************************/

#
# $Id: curriculum.php 4 2010-02-02 18:52:58Z kuzmich $
#

define('ADMIN_ZONE', true);

include_once ('../init.php');
include 'header.php';
include_once ('../include/curriculums.php');

if (isset($_REQUEST['school_year_id'])) {
	$school_year_id = intval($_REQUEST['school_year_id']);
} else {
  $school_year_id = 0;
}

if ($school_year_id == 0) {
$school_years = get_school_years();
?>
<br />
<h2>Список годов</h2><br />
<table id="rounded-corner">
  <thead>
  <tr>
    <th class="rounded-left">Учебный год</th>
    <th>Текущий</th>
    <th>Начало</th>
    <th>Окончание</th>
    <th class="rounded-right">&nbsp;</th>
  <tr>
  </thead>
  <tbody>
<?php foreach($school_years as $school_year) {?>
  <tr>
    <td nowrap="nowrap"><a href="curriculum.php?school_year_id=<?php echo $school_year['school_year_id']; ?>"><?php echo $school_year['name_year'];?></a></td>
    <td align="center"><?php if ($school_year['current']) {echo 'Да'; } ?></td>
    <td><?php echo $school_year['started'];?></td>
    <td><?php echo $school_year['finished'];?></td>
    <td nowrap="nowrap"><a href="school_year.php?school_year_id=<?php echo $school_year['school_year_id']; ?>&TB_iframe=true&keepThis=true&height=200&width=420&modal=true&<?php echo uniqid('sy'); ?>" title="Редактировать учебный год" class="thickbox">Редактировать</a>
        <a href="school_year.php?mode=drop&drop_school_year_id=<?php echo $school_year['school_year_id']; ?>&TB_iframe=true&keepThis=true&height=200&width=420&modal=true&<?php echo uniqid('sy'); ?>" title="Удалить учебный год" class="thickbox">Удалить</a>
    </td>
  </tr>
<?php } ?>
  </tbody>
<tfoot>
    	<tr>
       	  <td colspan="4" class="rounded-foot-left"></td>
        	<td class="rounded-foot-right"><a href="" onClick="javascript: tb_show('Добавить год', 'school_year.php?<?php echo uniqid('') ?>&TB_iframe=true&&keepThis=true&height=200&width=420&modal=true'); return false;" class="add" title="Добавить год">Добавить год</a>
</td>
        </tr>
    </tfoot>
</table>

<?php
} else {
  $quarters_in_year = get_quarters_in_year($school_year_id);
  $school_year = get_school_year($school_year_id);
?>
<br />
<h2><a href="curriculum.php">Список годов</a>&nbsp;&gt;&gt;&nbsp;<?php echo $school_year['name_year']; ?></h2><br />
<table id="rounded-corner">
  <thead>
  <tr>
    <th class="rounded-left">Названия периода</th>
    <th>Тип</th>
    <th>Текущий</th>
    <th>Начало</th>
    <th>Окончание</th>
    <th class="rounded-right">&nbsp;</th>
  <tr>
  </thead>
  <tbody>
<?php foreach($quarters_in_year as $quarter) {?>
  <tr>
    <td nowrap="nowrap"><?php echo $quarter['quarter_name'];?></td>
    <td align="center"><?php if ($quarter['quarter_type'] == 1) {echo 'Учеба'; } else {echo 'Каникулы';} ?></td>
    <td align="center"><?php if ($quarter['current']) {echo 'Да'; } ?></td>
    <td><?php echo $quarter['started'];?></td>
    <td><?php echo $quarter['finished'];?></td>
    <td><a href="quarter.php?quarter_id=<?php echo $quarter['quarter_id']; ?>&TB_iframe=true&height=200&width=400&<?php echo uniqid('sy'); ?>" title="Редактировать учебный период" class="thickbox">Редактировать</a></td>
  </tr>
<?php } ?>
  </tbody>
<tfoot>
    	<tr>
       	  <td colspan="5" class="rounded-foot-left"></td>
        	<td class="rounded-foot-right" nowrap="nowrap"><a href="" onClick="javascript: tb_show('Добавить четверть', 'quarter.php?<?php echo uniqid('') ?>&school_year_id=<?php echo $school_year_id;?>&keepThis=true&TB_iframe=true&height=200&width=400&modal=true'); return false;" class="add" title="Добавить учебный период">Добавить период</a>
</td>
        </tr>
    </tfoot>
</table>

<?php

}

include 'footer.php';
?>