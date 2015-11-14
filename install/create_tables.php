<?php
/*****************************************************************************\
+-----------------------------------------------------------------------------+
| SchoolReg                                                                   |
| Copyright (c) 2010 Z.                                                       |
| Copyright (c) 2009 Sergey V. Kuzin <sergey@kuzin.name>                      |
| All rights reserved.                                                        |
+-----------------------------------------------------------------------------+
\*****************************************************************************/
include_once '../include/mysql_db.php';

function db_exec_dml($target_name, &$log, $dmlsql) {
	$result = mysql_query($dmlsql);
	if ($result) {
		$log[] = array('name' => $target_name, 'result' => 'OK');
	} else {
		$log[] = array('name' => $target_name, 'result' => 'ERROR: '.mysql_error());
	}
	return $result;
}

function schoolreg_create_tables() {
	$resultLog = array();

db_exec_dml('classes', $resultLog,
"CREATE TABLE IF NOT EXISTS `classes` (
  `class_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `class` int(11) NOT NULL DEFAULT '1',
  `letter` char(2)  NOT NULL DEFAULT 'А',
  `school_year` int(4) NOT NULL,
  `teacher_id` int(11) NOT NULL DEFAULT '0',
  `school_year_id` bigint(20) NOT NULL,
  PRIMARY KEY (`class_id`),
  UNIQUE KEY `class` (`class`,`letter`,`school_year`)
) DEFAULT CHARSET=utf8;"
);

db_exec_dml('disciplines', $resultLog,
"CREATE TABLE IF NOT EXISTS `disciplines` (
  `discipline_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `discipline` varchar(50)  NOT NULL,
  PRIMARY KEY (`discipline_id`)
) DEFAULT CHARSET=utf8;"
);

db_exec_dml('lessons', $resultLog,
"CREATE TABLE IF NOT EXISTS `lessons` (
  `lesson_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `lesson_date` date NOT NULL,
  `subject_id` int(10) unsigned NOT NULL,
  `topic` varchar(255)  NOT NULL,
  `active` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`lesson_id`)
) DEFAULT CHARSET=utf8;"
);

db_exec_dml('quarters', $resultLog,
"CREATE TABLE IF NOT EXISTS `quarters` (
  `quarter_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `school_year_id` bigint(20) unsigned NOT NULL,
  `quarter_name` varchar(50) NOT NULL,
  `quarter_type` int(11) NOT NULL DEFAULT '1',
  `current` int(11) NOT NULL DEFAULT '0',
  `started` date NOT NULL,
  `finished` date NOT NULL,
  PRIMARY KEY (`quarter_id`),
  KEY `current` (`current`),
  KEY `school_year_id` (`school_year_id`),
  KEY `type_quarter` (`quarter_type`)
) DEFAULT CHARSET=utf8;"
);

db_exec_dml('schedules', $resultLog,
"CREATE TABLE IF NOT EXISTS `schedules` (
  `schedule_id` int(10) NOT NULL AUTO_INCREMENT,
  `quater_ref` int(10) NOT NULL,
  `class_ref` int(10) NOT NULL,
  `discipline_ref` int(10) NOT NULL,
  `day_of_week` int(2) NOT NULL,
  `num` int(3) DEFAULT '1',
  PRIMARY KEY (`schedule_id`),
  KEY `quater_class_discipline_refs` (`quater_ref`,`class_ref`,`discipline_ref`)
) DEFAULT CHARSET=utf8;"
);

db_exec_dml('school_years', $resultLog,
"CREATE TABLE IF NOT EXISTS `school_years` (
  `school_year_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name_year` varchar(50) NOT NULL,
  `current` int(11) NOT NULL DEFAULT '0',
  `started` date NOT NULL,
  `finished` date NOT NULL,
  PRIMARY KEY (`school_year_id`),
  KEY `current` (`current`)
) DEFAULT CHARSET=utf8;"
);

db_exec_dml('students', $resultLog,
"CREATE TABLE IF NOT EXISTS `students` (
  `student_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `last_name` varchar(25)  NOT NULL,
  `first_name` varchar(25)  NOT NULL,
  `middle_name` varchar(25)  NOT NULL,
  `birthday` date NOT NULL DEFAULT '0000-00-00',
  `address` varchar(255)  NOT NULL,
  `phone` varchar(25)  NOT NULL,
  `mother_fio` varchar(50)  NOT NULL,
  `mother_work_phone` varchar(25)  NOT NULL,
  `mother_cell_phone` varchar(25)  NOT NULL,
  `father_fio` varchar(50)  NOT NULL,
  `father_work_phone` varchar(25)  NOT NULL,
  `father_cell_phone` varchar(25)  NOT NULL,
  `pin_code` int(6) NOT NULL DEFAULT '0',
  `email` varchar(25)  NOT NULL,
  `smsphone` varchar(11)  NOT NULL,
  PRIMARY KEY (`student_id`),
  UNIQUE KEY `pin_code` (`pin_code`)
) DEFAULT CHARSET=utf8;"
);

db_exec_dml('students_in_class', $resultLog,
"CREATE TABLE IF NOT EXISTS `students_in_class` (
  `class_id` int(10) unsigned NOT NULL,
  `student_id` int(10) unsigned NOT NULL,
  `expeled` int(1) NOT NULL DEFAULT '0',
  KEY `class_id` (`class_id`,`student_id`)
) DEFAULT CHARSET=utf8;"
);

db_exec_dml('students_on_lesson', $resultLog,
"CREATE TABLE IF NOT EXISTS `students_on_lesson` (
  `student_id` int(10) unsigned NOT NULL,
  `lesson_id` int(10) unsigned NOT NULL,
  `grade` char(2)  NOT NULL,
  UNIQUE KEY `student_id` (`student_id`,`lesson_id`)
) DEFAULT CHARSET=utf8;"
);

db_exec_dml('subjects', $resultLog,
"CREATE TABLE IF NOT EXISTS `subjects` (
  `subject_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `discipline_id` int(10) unsigned NOT NULL,
  `teacher_id` int(10) unsigned NOT NULL,
  `class_id` int(11) NOT NULL,
  PRIMARY KEY (`subject_id`)
) DEFAULT CHARSET=utf8;"
);

db_exec_dml('teachers', $resultLog,
"CREATE TABLE IF NOT EXISTS `teachers` (
  `teacher_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `login` varchar(25)  NOT NULL,
  `passwd` varchar(32)  NOT NULL,
  `first_name` varchar(25)  NOT NULL,
  `middle_name` varchar(25)  NOT NULL,
  `last_name` varchar(25)  NOT NULL,
  PRIMARY KEY (`teacher_id`)
) DEFAULT CHARSET=utf8;"
);

db_exec_dml('users', $resultLog,
"CREATE TABLE IF NOT EXISTS `users` (
  `user_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `login` char(25) CHARACTER SET utf8 NOT NULL,
  `passwd` char(35) CHARACTER SET utf8 NOT NULL,
  `first_name` char(25) CHARACTER SET utf8 NOT NULL,
  `middle_name` char(25) CHARACTER SET utf8 NOT NULL,
  `last_name` char(25) CHARACTER SET utf8 NOT NULL,
  `access` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`)
) DEFAULT CHARSET=utf8;"
);

return $resultLog;
}

function schoolreg_upgrade_tables(&$log) {
	$result = db_query("SHOW COLUMNS FROM classes LIKE 'school_year_id'");
	if (!mysql_fetch_row($result)) {
		db_exec_dml('TABLE `classes` - ADD COLUMN `school_year_id`', $log,
		            "ALTER TABLE `classes` ADD COLUMN `school_year_id` bigint(20) NOT NULL after `teacher_id`;");
	}

	db_exec_dml('CREATE TABLE IF NOT EXISTS `lesson_types`', $log,
"CREATE TABLE IF NOT EXISTS `lesson_types`(
	`lesson_type_id` int(5) NOT NULL  auto_increment , 
	`lesson_type_name` varchar(100) NOT NULL  , 
	PRIMARY KEY (`lesson_type_id`) 
) DEFAULT CHARSET='utf8';"
    );
    
    $result = db_query("SHOW COLUMNS FROM lessons LIKE 'lesson_type_id'");
	if (!mysql_fetch_row($result)) {
		db_exec_dml('TABLE `lessons` - ADD COLUMN `lesson_type_id`', $log,
		            "ALTER TABLE `lessons` ADD COLUMN `lesson_type_id` int(5) NOT NULL DEFAULT '1' after `active`;");
	}
	db_exec_dml('TABLE `lessons` - ADD INDEXES `lesson_date`, `lesson_type_id`, `subject_id`', $log,
"ALTER TABLE `lessons` 
	ADD KEY `lesson_date`(`lesson_date`), 
	ADD KEY `lesson_type_id`(`lesson_type_id`), 
	ADD KEY `subject_id`(`subject_id`);"
    );

	$result = db_query("SHOW COLUMNS FROM students LIKE 'smsphone'");
    if (!mysql_fetch_row($result)) {
		db_exec_dml('TABLE `students` - ADD COLUMN `smsphone`', $log,
		            "ALTER TABLE `students` ADD COLUMN `smsphone` varchar(11)  after `email`;");
	}
    db_exec_dml('TABLE `students` - CHANGE COLUMN `smsphone`', $log, 
                "ALTER TABLE `students` CHANGE `phone` `phone` varchar(25)  after `address`;");

    db_exec_dml('TABLE `students_on_lesson` - CHANGE INDEX `student_id`', $log,
                "ALTER TABLE `students_on_lesson` DROP KEY `student_id`, add UNIQUE KEY `student_id`(`student_id`,`lesson_id`);");

    db_query("UPDATE classes SET school_year_id = (SELECT school_year_id FROM school_years WHERE DATE_FORMAT(started,'%Y') = school_year LIMIT 1)".
             " WHERE school_year_id=0 OR school_year_id IS NULL");
    db_query("INSERT IGNORE INTO `lesson_types` (`lesson_type_id`, `lesson_type_name`) values('1','Обычный урок')");
    db_query("INSERT IGNORE INTO `lesson_types` (`lesson_type_id`, `lesson_type_name`) values('2','Тематическое оценивание')");
}
	
?>