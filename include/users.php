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

function get_users_list()
{
  $sql = "SELECT * FROM users ORDER BY 5, 3, 4";
  $res = db_query($sql);
  $users_list = array();
  while($row = mysql_fetch_assoc($res)){
    $users_list[] = $row;
  }
  return $users_list;
}

function user_login($login, $password) {
	$sql = "SELECT user_id FROM users WHERE login='%s' AND passwd='%s' AND access=1";
	$res = db_query($sql,$login,md5($password));
	$row = mysql_fetch_assoc($res);
	return $row;
}

function add_user($login, $password, $first_name = null, $middle_name = null, $last_name = null) {
	$retArray = array('isError' => false);
	//check if user alresy exists
	$sql = "SELECT user_id FROM users WHERE login='%s'";
	$res = db_query($sql, $login);
	$row = mysql_fetch_row($res);
	if ($row) {
		$retArray['isError'] = true;
		$retArray['errorText'] = 'Пользователь с таким именем уже существует.'; 
	} else {
		$sql = "INSERT INTO users (login,passwd,first_name,middle_name,last_name,access) VALUES('%s','%s','%s','%s','%s',%n)";
		$res = db_query($sql, $login, md5($password), $first_name, $middle_name, $last_name, 1);
		if (!res) {
			$retArray['isError'] = true;
			$retArray['errorText'] = "INSERT MySQL error ".mysql_errno().": ".mysql_error();
		} else {
			$retArray['newUserId'] = db_get_insert_id();
		}
	}
	return $retArray;
}
?>