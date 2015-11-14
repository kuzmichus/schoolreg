<?php
/*****************************************************************************\
+-----------------------------------------------------------------------------+
| SchoolReg                                                                   |
| Copyright (c) 2010 Z.                                                       |
| Copyright (c) 2009 Sergey V. Kuzin <sergey@kuzin.name>                      |
| All rights reserved.                                                        |
+-----------------------------------------------------------------------------+
\*****************************************************************************/

/**
 * Return true when HTTP_X_REQUESTED_WITH HTTP header is set to XMLHttpRequest 
 * (JQuery set it during ajax calls)
 */
function isAjax ()
{
	$result=false;
	if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])&& $_SERVER['HTTP_X_REQUESTED_WITH'] == "XMLHttpRequest") {
		$result=true;
	} 
	return $result;
}
?>
