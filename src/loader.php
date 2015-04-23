<?php

/**
 * Loaded files container
 * 
 * @param string $file
 * @param boolean $loaded
 * @return mixed
 */
function loads ($file = null, $loaded = null) {
    static $repo = null;    
    $repo or $repo = repo();
    
    return $repo($file, $loaded);
}

/**
 * Load a php file
 * 
 * @param string $file
 * @param bool $ignore
 * @param string $ext
 */
function load_php ($file, $ignore = false, $ext = '.php') {
    if (ends_with($file, '.php')) {
        $file = substr($file, 0, -4);
    }
    
    $filepath = $file . $ext;
    $exists   = file_exists($filepath);
    
    if ($exists && !loads($file) || $ignore) {
        loads($file, true);
        
        return require $filepath;
    }
    else if (!$exists) {
        throw new Exception("File at '$filepath' is not exists!");
    }
}

/**
 * Alias to load files in app directory
 *
 * @param string $file
 * @param bool $ignore
 * @return mixed
 */
function load_app_file ($file, $ignore = false) {
    return load_php(app_path($file), $ignore);
}

/**
 * Alias to load files in api directory
 *
 * @param string $file
 * @param bool $ignore
 * @return mixed
 */
function load_api ($file, $ignore = false) {
    return load_php(api_path($file), $ignore);
}

/**
 * Load files
 * 
 * @param array $files
 */
function load_files ($files) {
    if (empty($files)) {
        return false;
    }
    
    foreach ($files as $file) {
        load_php($file);
    }
}

/**
 * Load model functions 
 * 
 * @param string $model
 */
function load_model ($model, $path = 'models') {
    if (file_exists(app_path("$path/$model.php"))) {
        load_app_file("$path/$model");
    }
    
    function_exists($model = "{$model}_init") and $model();
}

/**
 * Load system dependencies
 */
function load_system () {
    $api = array('router', 'events', 'view', 'database', 'input', 'i18n');
    
    foreach ($api as $script) {
        load_api($script);
    }
}