<?php

/**
 * mini_framework constants
 * 
 * @const string MF_VERSION Version of mini_blog
 * @const string MF_API_DIR Path to system files
 */
define('MF_VERSION', '1.2');
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
 */
function app_boot ($config) {
    $config = config($config);
    
    system_load($config);
    app_load($config);
    
    emit('router:pre_dispatch');
    
    dispatch(fetch_route(get_url(), $_SERVER['REQUEST_METHOD']));
    
    emit('router:post_dispatch');
}

/**
 * Loads the system
 * 
 * Useful for unit testing purposes or exclude routing from loading
 * process
 */
function system_load ($config) {
    load_system();
    
    router('settings', $config('routing'));
    router('settings.root', get_baseurl(base_path(), $_SERVER['DOCUMENT_ROOT']));
    
    views('templates', $config('templates'));
    lang('settings', $config('i18n'));
    
    db($config('database'));
    
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