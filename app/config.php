<?php

/**
 * App's config
 */

return array(
	'database' => array(
		'autoload' => false,
		'default'  => array(
			'host' => 'localhost',
			'user' => 'root',
			'name' => '',
			'password' => '',
			'charset' => 'utf8'
		)
	),
	
	'templates' => array(
		'directory' => MF_BASEPATH . 'templates/',
		'template' => 'default'
	),
	
	'routing' => array(
		'base_url' => 'http://ffframework.dev/',
		'symbols' => [
			'/:any' => '/?([\d\w\-_]+)',
			'/:num' => '/?(\d+)'
		],
		'root' => ''
	),
	
	'hooks' => array(
		MF_APP_DIR . 'bootstrap',
		MF_APP_DIR . 'routes'
	),
	
	'autoload' => array(
		'models' => []
	),
	
	'i18n' => array(
	    'default' => 'ru_RU'
	)
);