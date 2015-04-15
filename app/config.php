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
		'symbols' => array(
			'/:any' => '/?([\d\w\-_]+)',
			'/:num' => '/?(\d+)'
		),
		'root' => ''
	),
	
	'hooks' => array(
		MF_APP_DIR . 'bootstrap',
		MF_APP_DIR . 'routes'
	),
	
	'autoload' => array(
		'models' => array(),
		'files'  => array()
	),
	
	'i18n' => array(
	    'default' => 'ru_RU'
	)
);