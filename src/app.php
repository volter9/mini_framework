<?php namespace app;

use db;
use events;
use loader;
use router;
use storage;
use view;

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
define('MF_VERSION', '2.0.0');
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
function boot ($config, $auto_dispatch = false) {
    $config = storage\config($config);
    
    system_load($config);
    load($config);
    
    events\emit('router:pre_dispatch');
    
    dispatch(router\get_url(), $auto_dispatch);
    
    events\emit('router:post_dispatch');
}

/**
 * Dispatch application's routes
 * 
 * @param string $url
 */
function dispatch ($url, $auto_dispatch) {
    $method = array_get($_SERVER, 'REQUEST_METHOD', 'GET');
    $result = $auto_dispatch
        ? router\auto_dispatch($url)
        : router\dispatch(router\fetch($url, $method));
    
    if ($result === false) {
        view\not_found();
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
    loader\system($config('autoload.modules'));
    
    if (function_exists('router\storage')) {
        $root = array_get($_SERVER, 'DOCUMENT_ROOT', base_path());
        
        router\storage('settings', $config('routing'));
        router\storage('settings.root', router\base_url(base_path(), $root));
    }
    
    function_exists('view\storage') and view\storage('settings', $config('templates'));
    function_exists('lang\storage') and lang\storage('settings', $config('i18n'));
    function_exists('db\db')        and db\db($config('database'));
    
    storage\shared('validation', $config('validation'));
    storage\shared('config', $config);
}

/**
 * Load app's components
 * 
 * @param callable $config
 */
function load ($config) {
    if ($config('database.autoload')) {
        db\connect();
    }
    
    loader\files($config('autoload.files'));
    loader\files($config('hooks'));
    
    load_models($config('autoload.models'));
}

/**
 * Load models
 * 
 * @param array $models
 * @return bool
 */
function load_models ($models) {
    if (empty($models)) {
        return false;
    }
    
    foreach ($models as $model) {
        loader\model($model);
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