<?php

/**
 * FFFramework constants
 * 
 * @const string MF_VERSION Version of mini_blog
 * @const string MF_API_DIR Path to system files
 */
define('MF_VERSION', '1.0');
define('MF_API_DIR', __DIR__ . '/');

/**
 * Head start requirements
 */
require MF_API_DIR . 'utils.php';
require MF_API_DIR . 'loader.php';
require MF_API_DIR . 'config.php';

/**
 * Boot the app
 */
function app_boot ($config) {
	$config = config($config);
	
	system_load($config);
	app_load($config);
	
	emit('router:pre_dispatch');
	
	dispatch(fetch_route(get_url(), $_SERVER['REQUEST_METHOD']));
}

/**
 * Loads the system
 * 
 * Useful for unit testing purposes
 */
function system_load ($config) {
	load_system();
	
	router('settings', $config('routing'));
	router('settings.root', get_base_url(MF_BASEPATH, $_SERVER['DOCUMENT_ROOT']));
	
	views('templates', $config('templates'));
	lang('settings', $config('i18n'));
	
	db($config('database'));
}

/**
 * Load app's components
 * 
 * @param callable $config
 */
function app_load ($config) {
	if ($config('database.autoload')) {
		db_connect();
	}
	
	load_files($config('autoload.files'));
	load_files($config('hooks'));
	app_load_models($config('autoload.models'));
}

/**
 * Load models
 * 
 * @param array $models
 * @return bool
 */
function app_load_models ($models) {
	if (empty($models)) {
		return false;
	}
	
	foreach ($models as $model) {
		load_model($model);
	}
}