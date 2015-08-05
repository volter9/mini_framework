<?php namespace router;

use app;
use events;
use loader;
use storage;

/**
 * HTTP routing and URL generation (mostly)
 * 
 * @package mini_framework
 * @require app
 * @require events
 * @require storage
 * @require loader
 */

/**
 * @param array $data
 */
function init (array $data) {
    $path = app\base_path();
    $root = array_get($_SERVER, 'DOCUMENT_ROOT', $path);
    
    storage('settings', $data);
    storage('settings.root', base_url($path, $root));
}

/**
 * Router storage
 * 
 * @param string $key
 * @param mixed $value
 * @return mixed
 */
function storage ($key = null, $value = null) {
    static $repo = null;
    
    $repo or $repo = storage\repo(array(
        'routes' => array()
    ));
    
    return $repo($key, $value);
}

/**
 * Route a url to action
 * 
 * @param string $url
 * @param string $action
 */
function map ($url, $action) {
    $route = parse($url, $action);
    
    storage("routes.{$route['id']}", $route);
}

/**
 * Find route
 * 
 * @param string $url
 * @param string $method
 * @return array|bool
 */
function find ($url, $method) {
    $routes = storage('routes');
    
    foreach ($routes as $found) {
        $routeUrl = process($found['url']);
        $pattern  = "#^{$routeUrl}\$#i";
        
        $correct_method = in_array($found['method'], array('*', $method));
        $matched_url    = preg_match($pattern, $url, $matches);
        
        if ($correct_method && $matched_url) {
            $matches = array_numerify($matches);
            
            array_shift($matches);
            
            return compact('found', 'matches');
        }
    }
    
    return false;
}

/**
 * Process a route
 * 
 * @param string $url
 * @return string
 */
function process ($url) {
    static $symbols = null;
    
    if (!$symbols) {
        $symbols = storage('settings.symbols');
        $symbols or $symbols = array();
    }
    
    $find    = array_keys($symbols);
    $replace = array_values($symbols);
    
    return str_replace($find, $replace, $url);
}

/**
 * Parse a full route from url and action strings
 * 
 * @param string $url
 * @param string $action
 * @return array
 */
function parse ($url, $action) {
    list($method, $id, $url) = parse_url($url);
    
    $action = parse_action($action);
    $url    = trim($url, '/ ');
    
    return compact('method', 'id', 'url', 'action');
}

/**
 * Parse the passed URL in router\map function
 * 
 * @param string $url
 * @return array
 */
function parse_url ($url) {
    $fragments = explode(' ', $url);
    $count     = count($fragments);
    $id        = count(storage('routes')) + 1 . '';
    
    if ($count === 2) {
        array_splice($fragments, 1, 0, $id);
    }
    else if ($count === 1) {
        array_unshift($fragments, $id);
        array_unshift($fragments, '*');
    }
    
    return $fragments;
}

/**
 * Parse the passed action in router\map function
 * 
 * @param string $action
 * @return array|string
 */
function parse_action ($action) {
    if (is_callable($action)) {
        return $action;
    }
    
    $file = $action;
    $name = 'index';
    $namespace = '';
    
    if (strpos($action, ':') !== false) {
        list($file, $name) = explode(':', $action);
    }
    
    if (starts_with($name, '\\')) {
        $namespace = before_last($name, '\\');
        $name = after($name, '\\');
    }
    else {
        $namespace = exclude($file, app\base_path());
        $namespace = str_replace('/', '\\', "/$namespace");
    }
    
    return compact('file', 'name', 'namespace');
}

/**
 * Replace route url with parameters
 * 
 * @param string $url
 * @param array $params
 */
function replace ($url, array $params) {
    $regex = '/:(\w+)\??/';
    
    if (count($params)) {
        $regex = array_fill(0, count($params), '/:(\w+)\??/');
    }
    else {
        $params = '';
    }
    
    return cleanup(trim(preg_replace($regex, $params, $url, 1), '/ '));
}

/**
 * Clean up the route's URL
 * 
 * @param string $url
 * @return string
 */
function cleanup ($url) {
    return chop(preg_replace('/:(\w+)\??/', '', $url), '/ ');
};

/**
 * Execute order 66
 * 
 * @param string $url
 * @param string $method
 * @return array
 */
function fetch ($url, $method) {
    if (!$found = find($url, $method)) {
        return false;
    }
    
    events\emit('router:found', $found['found'], $found['matches']);
    
    return $found;
}

/**
 * Dispatch routing
 * 
 * @param array|bool $found
 * @return mixed
 */
function dispatch ($found) {
    if (!$found) {
        return false;
    }
    
    storage('route', $found);
    
    $route = $found['found'];
    
    if (!is_callable($route['action'])) {
        loader\php($route['action']['file']);
    }
    
    return invoke($route, $found['matches']);
}

/**
 * Auto dispatch
 * 
 * @param string $url
 * @return mixed
 */
function auto_dispatch ($url) {
    $fragments = explode('/', $url);
    
    $controller = array_shift($fragments);
    $controller = $controller ? $controller : 'index';
    
    $action = array_shift($fragments);
    $action = $action ? $action : 'index';
    
    try {
        loader\app_file("actions/$controller");
    }
    catch (Exception $e) {
        return false;
    }
    
    $route = array('action' => parse_action("actions/$controller:$action"));
    
    return invoke($route, $fragments);
}

/**
 * Invoke the route
 * 
 * @param array $action
 * @param array $parameters
 * @return mixed
 */
function invoke (array $route, array $parameters) {
    $action = $route['action'];
    
    if (!is_callable($action)) {
        $ns     = $action['namespace'];
        $action = $action['name'];
        
        $action = "$ns\\$action";
        $init   = "$ns\\init";
        
        if (function_exists($init) && $init() === false) {
            return false;
        }
        
        if (!function_exists($action)) {
            return false;
        }
    }
    
    return call_user_func_array($action, $parameters);
}

/**
 * Get request url
 * 
 * @return string
 */
function get_url () {
    $root = storage('settings.root');
    $url  = \parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    
    if ($root && strpos($url, $root) !== false) {
        $url = explode($root, $url);
        $url = end($url);
    }
    
    return trim($url, ' /');
}

/**
 * Get base URL
 * 
 * @param string $base
 * @param string $root
 * @return string
 */
function base_url ($base, $root) {
    $base   = trim($base, '/');
    $root   = trim($root, '/');
    $lenght = strlen($root);

    return $base === $root ? '' : trim(substr($base, $lenght), '/');
}

/**
 * Create a link to route 
 * 
 * @param string $id
 * @param array $params
 * @return string
 */
function url ($id, $params = array()) {
    if (!$route = storage("routes.$id")) {
        return '';
    }
    
    return path(replace($route['url'], $params));
}

/**
 * Get URL path from root to file
 * 
 * @param string $path
 * @return string
 */
function path ($path = '') {
    $root = storage('settings.root');
    $root = $root ? $root : '';
    
    $basepath = chop("/$root", '/'); 
    
    return "$basepath/$path";
}

/**
 * Redirect to route
 * 
 * @see url
 * @see redirect_path
 */
function redirect ($id, $params = array(), $exit = true) {
    redirect_path(url($id, $params), $exit);
}

/**
 * Redirect to URL relative to the website location
 * 
 * @see path
 * @see redirect_path
 */
function redirect_url ($url, $exit = true) {
    redirect_path(path($url), $exit);
}

/**
 * Redirect to path
 * 
 * @param string $path
 * @param bool $exit
 */
function redirect_path ($path, $exit = true) {
    header("Location: $path");
    
    if ($exit) {
        exit;
    }
}