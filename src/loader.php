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
function php ($file, $ignore = false, $ext = '.php') {
    static $loads = array();
    
    if (ends_with($file, $ext)) {
        $file = substr($file, 0, -strlen($ext));
    }
    
    $filepath = $file . $ext;
    $exists   = file_exists($filepath);
    
    if ($exists && (!isset($loads[$file]) || $ignore)) {
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
 * @param bool $ignore
 * @return mixed
 */
function api ($file, $ignore = false) {
    return php(app\api_path($file), $ignore);
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
function model ($model, $path = 'app/models') {
    if (file_exists(app\base_path("$path/$model.php"))) {
        php("$path/$model");
        
        function_exists($model = "{$model}_init") and $model();
    }
}

/**
 * Load system dependencies
 * 
 * @param array $modules
 */
function system ($modules = null) {
    $api = array('router', 'events', 'view', 'database', 'input', 'i18n');
    $api = empty($modules) ? $api : $modules;
    
    foreach ($api as $script) {
        api($script);
    }
}