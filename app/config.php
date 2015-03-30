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
		'directory' => FFF_BASEPATH . 'templates/',
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
		FFF_APP_DIR . 'bootstrap',
		FFF_APP_DIR . 'routes'
	],
	
	'autoload' => [
		'models' => []
	]
];