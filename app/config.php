<?php

/**
 * App's config
 */

return [
	'database' => [
		'autoload' => false,
		'default' => [
			'host' => 'localhost',
			'user' => 'root',
			'name' => '',
			'password' => '',
			'charset' => 'utf8'
		]
	],
	
	'templates' => [
		'directory' => MF_BASEPATH . 'templates/',
		'template' => 'default'
	],
	
	'routing' => [
		'base_url' => 'http://ffframework.dev/',
		'symbols' => [
			'/:any' => '/?([\d\w\-_]+)',
			'/:num' => '/?(\d+)'
		],
		'root' => ''
	],
	
	'hooks' => [
		MF_APP_DIR . 'bootstrap',
		MF_APP_DIR . 'routes'
	],
	
	'autoload' => [
		'models' => []
	]
];