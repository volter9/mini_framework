<?php namespace loader;

use app;

use Exception;

/**
 * mini_framework loader functions
 * 
 * @package mini_framework
 * @require app
 * @require string
 */

/**
 * Load a php file
 * 
 * @param string $file
 * @param bool $ignore
 * @param string $ext
 */
function php ($file, $ignore = false) {
    static $loads = array();
    
    if (ends_with($file, '.php')) {
        $file = substr($file, 0, -strlen('.php'));
    }
    
    $filepath = "$file.php";
    
    if (file_exists($filepath) && (!isset($loads[$file]) || $ignore)) {
        $loads[$file] = true;
        
        return require $filepath;
    }
    
    throw new Exception("File at '$filepath' is not exists!");
}

/**
 * Alias to load files in app directory
 *
 * @param string $file
 * @param bool $ignore
 * @return mixed
 */
function app_file ($file, $ignore = false) {
    return php(app\app_path($file), $ignore);
}

/**
 * Alias to load files in api directory
 *
 * @param string $file
 * @param array $args
 * @return mixed
 */
function api ($file, array $args = array()) {
    php(app\api_path($file));
    
    $func = $func = "\\$file\\init";
    
    function_exists($func) and $func($args);
}

/**
 * Load files
 * 
 * @param array $files
 */
function files ($files) {
    if (empty($files) || !is_array($files)) {
        return false;
    }
    
    foreach ($files as $file) {
        php($file);
    }
}

/**
 * Load model functions 
 * 
 * @param string $model
 * @param string $path
 */
function model ($model, $path = '') {
    $path = $path ? $path : str_replace('\\', '/', $model);
    $path = trim($path, '/');
    
    if (file_exists(app\base_path("$path.php"))) {
        php($path);
        
        function_exists($model = "$model\\init") and $model();
    }
}

/**
 * Load system dependencies
 * 
 * @param array $modules
 * @param array $data
 */
function system ($modules = null, array $data = array()) {
    $api = array('router', 'events', 'view', 'db', 'input', 'i18n');
    $api = empty($modules) ? $api : $modules;
    
    foreach ($api as $script) {
        api($script, array_get($data, $script, array()));
    }
}