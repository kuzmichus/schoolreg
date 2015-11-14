<?php
/*****************************************************************************\
+-----------------------------------------------------------------------------+
| SchoolReg                                                                   |
| Copyright (c) 2010 Z.                                                       |
| Copyright (c) 2009 Sergey V. Kuzin <sergey@kuzin.name>                      |
| All rights reserved.                                                        |
+-----------------------------------------------------------------------------+
\*****************************************************************************/

define('CONFIG_FILE', '../config.php');

session_start();

require_once '../lib/Twig/Autoloader.php';
require_once '../lib/file_put_contents.php';
Twig_Autoloader::register();

$twig_loader = new Twig_Loader_Filesystem(dirname(__FILE__).'/../lib/templates');
$twig = new Twig_Environment($twig_loader, array('cache' => dirname(__FILE__).'/../cache', 'trim_blocks' => true, 'auto_reload' => true));
$stepDesc = array('1' => 'Создание файла конфигурации',
                  '2' => 'Создание таблиц',
                  '3' => 'Модификация и начальное наполнение таблиц'); 

if (isset($_REQUEST['step'])) {
	$step = intval($_REQUEST['step']);
	if (isset($_REQUEST['action']) and $_REQUEST['action'] == 'next') {
		$step = $step + 1;
	}
} else {
  $step = 1;
}
if ($step >= 4) {
	header('Location: ../admin/index.php');
}

$template_name = '';
$renderArray = array();

if ($step == 1) {
	if (isset($_REQUEST['action']) and $_REQUEST['action'] == 'save') {
		$template_name = 'install-step1-result.html';
		$config_content = generate_config($twig);
		$result = save_config($config_content);
		if ($result) {
			$renderArray['resultText'] = 'Файл конфигурации был успешно сохранен!';
		} else {
			$renderArray['resultText'] = 'Файл конфигурации невозможно сохранить. Вам необходимо сделать это вручную';
			$renderArray['confFileContent'] = $config_content;
		}
	} else {
		$template_name = 'install-step1.html'; 
		if (file_exists(CONFIG_FILE)) {
			include_once CONFIG_FILE;
			if (!isset($config) or !is_array($config)) {
				$renderArray['message1'] = 'Файл конфигурации необходимо обновить';
				$renderArray['isNewConfig'] = false;
				$renderArray['conf_db_host'] = $db_host;
				$renderArray['conf_db_user'] = $db_user;
				$renderArray['conf_db_passwd'] = $db_passwd;
				$renderArray['conf_db_base'] = $db_base;
				$renderArray['conf_epochtasms_login'] = $epochtasms_login;
				$renderArray['conf_epochtasms_passwd'] = $epochtasms_passwd;
				$renderArray['conf_epochtasms_from'] = $epochtasms_from;
			} else {
				$template_name = 'install-step1-result.html';
				$renderArray['resultText'] = 'Файл конфигурации уже существует!';
			}
		} else {
			$renderArray['isNewConfig'] = true;
			$renderArray['conf_db_host'] = 'localhost';
			$renderArray['conf_db_base'] = 'schoolreg';
		}
	}

} elseif ($step >= 2) {
	$template_name = 'install-step-result.html';
	try {
		@include_once CONFIG_FILE;
		include_once dirname(__FILE__).'/create_tables.php';
		include_once '../include/mysql_db.php';
		
		if (!checkConfig()) {
			throw new Exception('Конфигурационный файл отсутствует или неверен.');
		}
		$link = @mysql_connect($config['db']['db_host'], $config['db']['db_user'], $config['db']['db_passwd']); 
		if (!$link) {
			throw new Exception('Ошибка подсоединения к базе, проверьте конфигурационные параметры. Текст ошибки: '.mysql_error());
		} 
		if (!@mysql_select_db($config['db']['db_base'], $link)) {
			throw new Exception('Ошибка выбора базы, проверьте конфигурационные параметры. Текст ошибки: '.mysql_error());
		}
		if (!@mysql_query('SET NAMES UTF8;')) {
			throw new Exception('Ошибка установки client character set в UTF8. Текст ошибки: '.mysql_error());
		}
		
		if ($step == 2) {
			$result = db_query("SHOW TABLES LIKE 'users'");
			if (mysql_fetch_row($result)) {
				$renderArray['resultText'] = 'Таблицы в базе уже существуют!';
			} else {
				$renderArray['log'] = schoolreg_create_tables();
			}
			$result = db_query("SELECT user_id FROM users");
			if (!mysql_fetch_row($result)) {
				$renderArray['addNewUser'] = true;
			}
		} elseif ($step == 3) {
			$renderArray['log'] = array();
			if (isset($_REQUEST['user_name']) and isset($_REQUEST['user_pwd'])) {
				include_once '../include/users.php';
				$res = add_user($_REQUEST['user_name'], $_REQUEST['user_pwd']);
				$renderArray['log'][] = array('name' => 'Add new user'
				                            , 'result' => $res['isError'] ? 'ERROR: '.$res['errorText'] : 'OK' );
			}
			schoolreg_upgrade_tables($renderArray['log']);
			$renderArray['resultText'] = 'Все действия по установке системы выполнены.';
		}
			
	} catch (Exception $e) {
		$renderArray['ErrorText'] = $e->getMessage();
	}
	
}

$renderArray['step'] = $step;
$renderArray['stepDesc'] = $stepDesc[$step];
$template = $twig->loadTemplate('install/'.$template_name);
echo $template->render($renderArray);

function generate_config($twig) {
	$renderConfArray = array();
	$renderConfArray['db_server'] = $_REQUEST['db_server'];
	$renderConfArray['db_login'] = $_REQUEST['db_login'];
	$renderConfArray['db_passwd'] = $_REQUEST['db_passwd'];
	$renderConfArray['db_name'] = $_REQUEST['db_name'];
	$renderConfArray['epochtasms_login'] = $_REQUEST['epochtasms_login'];
	$renderConfArray['epochtasms_passwd'] = $_REQUEST['epochtasms_passwd'];
	$renderConfArray['epochtasms_from'] = $_REQUEST['epochtasms_from'];
	$conf_template = $twig->loadTemplate('install/config.php.tpl');
    return $conf_template->render($renderConfArray);
}

function save_config($conf_content) {
	return @file_put_contents(CONFIG_FILE,$conf_content);	
}

function checkConfig() {
	global $config;
	return (isset($config) and is_array($config) and is_array($config['db']));
}
?>
