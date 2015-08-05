<?php namespace view;

use storage;
use router;

use Exception;

/**
 * View component
 * 
 * @package mini_framework
 * @require storage
 * @require string
 */

function init (array $data) {
    storage('settings', $data);
}

/**
 * Views repository
 * 
 * @param string $key
 * @param mixed $value
 * @return mixed
 */
function storage ($key = null, $value = null) {
    static $repo = null;
    
    $repo or $repo = storage\repo();
    
    return $repo($key, $value);
}

/**
 * View a layout
 * 
 * @param string $view
 * @param array $data
 */
function layout ($view, array $data = array()) {
    $data['view'] = $view;
    
    partial(storage('settings.layout'), $data);
}

/**
 * View a partial view
 * 
 * @param string $view
 * @param array $data
 * @param bool $global
 */
function partial ($view, $data = array(), $global = true) {
    if ($global) {
        storage('data', $data);
    }
    
    render(path($view), empty($data) ? storage('data') : $data);
}

/**
 * Show page 404
 */
function not_found () {
    header('HTTP/1.1 404 Not Found');
    
    partial('404') xor exit;
}

/**
 * Isolation function from view's function context
 * 
 * @param string $__view__
 * @param array $__data__
 */
function render ($__view__, array $__data__) {
    extract($__data__);
    
    require($__view__);
}

/**
 * Show an error
 * 
 * @param Exception $exception
 */
function error (Exception $exception) {
    partial('error', compact('exception')) xor exit;
}

/**
 * Get template name from 
 * 
 * @param string $template
 * @return array
 */
function parse_template ($template) {
    $contains = contains($template, ':');
    
    return array( 
        $contains ? before($template, ':') : storage('settings.template'),
        $contains ? after($template,  ':') : $template
    );
}

/**
 * Transforms a template name to full template path
 * 
 * @param string $view
 * @return string
 */
function path ($view) {
    if (starts_with($view, '/')) {
        return "$view.php";
    }
    
    $directory = chop(storage('settings.directory'), '/');
    
    list($template, $view) = parse_template($view);
    
    return $template ? "$directory/$template/html/$view.php" : "$directory/$view.php";
}

/**
 * URL to template file
 * 
 * @param string $file
 * @return string
 */
function asset_url ($file = '') {
    list($template, $file) = parse_template($file);
    
    $folder = after(chop(storage('settings.directory'), '/'), '/');
    $root   = storage('settings.root');
    
    return deduplicate("/$root/$folder/$template/$file", '/');
}

/**
 * Path to template file
 * 
 * @param string $file
 * @return string
 */
function asset_path ($file = '') {
    list($template, $file) = parse_template($file);
    
    $directory = storage('settings.directory');
    
    return "$directory/$template/$file";
}

/**
 * Capture given callback's output
 * 
 * @param callable $callback
 * @return string
 */
function capture ($callback) {
    ob_start();
    
    $callback();
    
    return ob_get_clean();
}