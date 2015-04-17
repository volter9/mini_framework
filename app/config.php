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
		'directory' => base_path('views/'),
		'template'  => 'default',
		'layout'    => 'main'
	),
	
	'routing' => array(
		'base_url' => 'http://ffframework.dev/',
		'symbols' => array(
			'/:any' => '/?([\d\w\-_]+)',
			'/:num' => '/?(\d+)'
		)
	),
	
	'hooks' => array(
		app_path('bootstrap'),
		app_path('routes')
	),
	
	'autoload' => array(
		'models' => array(),
		'files'  => array()
	),
	
	'i18n' => array(
	    'default' => 'ru_RU'
	),
	
	'validation' => array(
	    'validators' => 'validators'
	)
);