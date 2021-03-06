<?php

/**
 * Application initialization functions
 * 
 * @package mini_framework
 * @require storage
 * @require loader
 */

/**
 * mini_framework constants
 * 
 * @const string MF_VERSION Version of mini_blog
 * @const string MF_API_DIR Path to system files
 */
define('MF_VERSION', '1.2.3');
define('MF_API_DIR', __DIR__ . '/');

/**
 * Head start requirements
 */
require api_path('array.php');
require api_path('string.php');
require api_path('loader.php');
require api_path('storage.php');

/**
 * Boot the app
 * 
 * @param string $config
 * @param bool $auto_dispatch
 */
function app_boot ($config, $auto_dispatch = false) {
    $config = config($config);
    
    system_load($config);
    app_load($config);
    
    emit('router:pre_dispatch');
    
    app_dispatch(get_url(), $auto_dispatch);
    
    emit('router:post_dispatch');
}

/**
 * Dispatch application's routes
 * 
 * @param string $url
 */
function app_dispatch ($url, $auto_dispatch) {
    $method = array_get($_SERVER, 'REQUEST_METHOD', 'GET');
    
    if (
        $auto_dispatch && auto_dispatch(get_url()) === false ||
        !$auto_dispatch && dispatch(fetch_route($url, $method)) === false
    ) {
        not_found();
    }
}

/**
 * Loads the system
 * 
 * Useful for unit testing purposes or excluding routing from 
 * loading process
 * 
 * @param callable $config
 */
function system_load ($config) {
    $root = array_get($_SERVER, 'DOCUMENT_ROOT', base_path());
    
    load_system($config('autoload.modules'));
    
    if (function_exists('router')) {
        router('settings', $config('routing'));
        router('settings.root', get_baseurl(base_path(), $root));
    }
    
    function_exists('views') and views('templates', $config('templates'));
    function_exists('lang')  and lang('settings', $config('i18n'));
    function_exists('db')    and db($config('database'));
    
    storage('validation', $config('validation'));
    storage('config', $config);
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

/**
 * Get path relative from basepath to file
 * 
 * @param string $file
 * @return string
 */
function base_path ($file = '') {
    return MF_BASEPATH . $file;
}

/**
 * Get path to app file
 * 
 * @param string $file
 * @return string
 */
function app_path ($file = '') {
    return MF_APP_DIR . $file;
}

/**
 * Get path to api file
 * 
 * @param string $file
 * @return string
 */
function api_path ($file = '') {
    return MF_API_DIR . $file;
}