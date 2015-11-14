<?php 
/*****************************************************************************\
+-----------------------------------------------------------------------------+
| SchoolReg                                                                   |
| Copyright (c) 2010 Z.                                                       |
| Copyright (c) 2009 Sergey V. Kuzin <sergey@kuzin.name>                      |
| All rights reserved.                                                        |
+-----------------------------------------------------------------------------+
\*****************************************************************************/

$config = array();
$config['db']['db_host'] = '{{db_server}}';
$config['db']['db_user'] = '{{db_login}}';
$config['db']['db_passwd'] = '{{db_passwd}}';
$config['db']['db_base'] = '{{db_name}}';

$config['epochtasms']['login'] = '{{epochtasms_login}}';
$config['epochtasms']['passwd'] = '{{epochtasms_passwd}}';
$config['epochtasms']['from'] = '{{epochtasms_from}}';

$config['ui']['theme'] = 'south-street';

$config['class']['numbers'] = range(1, 12);
$config['class']['letters'] = array('А','Б','В','Г','Д');

?>
