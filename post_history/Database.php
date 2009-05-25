<?php

global $addSettings, $permissions, $tables;

if (!defined('SMF'))
	die('Hacking attempt...');

$addSettings = array(
	'posthistoryEnabled' => array(true, false),
);

$permissions = array(
);

$tables = array(
	'messages_history' => array(
		'name' => 'messages_history',
		'columns' => array(
			array(
				'name' => 'id_edit',
				'type' => 'int',
				'auto' => true,
				'unsigned' => true,
			),
			array(
				'name' => 'id_msg',
				'type' => 'int',
				'default' => 0,
				'unsigned' => true,
			),
			array(
				'name' => 'modified_name',
				'type' => 'varchar',
				'size' => 255,
				'default' => '',
			),
			array(
				'name' => 'modified_time',
				'type' => 'int',
				'default' => 0,
				'unsigned' => true,
			),
			array(
				'name' => 'body',
				'type' => 'text',
			),
		),
		'indexes' => array(
			array(
				'type' => 'primary',
				'columns' => array('id_edit')
			),
			array(
				'type' => 'index',
				'columns' => array('id_msg')
			),
		)
	),
);

?>