<?php

/**
 * HTTP routing (mostly)
 * 
 * @package mini_framework
 * @require events
 * @require storage
 * @require loader
 */

/**
 * Router storage
 * 
 * @param string $key
 * @param mixed $value
 * @return mixed
 */
function router ($key = null, $value = null) {
    static $repo = null;
    
    $repo or $repo = repo(array(
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
function route ($url, $action) {
    $route = parse_route($url, $action);
    
    router("routes.{$route['id']}", $route);
}

/**
 * Find route
 * 
 * @param string $url
 * @param string $method
 * @return array|bool
 */
function router_find ($url, $method) {
    $routes = router('routes');
    
    foreach ($routes as $found) {
        $routeUrl = route_process($found['url']);
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
function route_process ($url) {
    static $symbols = null;
    $symbols or $symbols = router('settings.symbols');
    
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
function parse_route ($url, $action) {
    $name = 'index';
    $file = $action;
    
    list($method, $id, $url) = explode(' ', $url);
    
    if (is_string($action)) {
        if (strpos($action, ':') !== false) {
            list($file, $name) = explode(':', $action);
        }
        
        $action = compact('file', 'name');
    }
    
    $url = trim($url, '/ ');
    
    return compact('method', 'id', 'url', 'action');
}

/**
 * Replace route url with parameters
 * 
 * @param string $url
 * @param array $params
 */
function route_replace ($url, array $params) {
    $regex = '/:(\w+)\??/';
    
    if (count($params)) {
        $regex = array_fill(0, count($params), '/:(\w+)\??/');
    }
    else {
        $params = '';
    }
    
    return route_cleanup(trim(preg_replace($regex, $params, $url, 1), '/ '));
}

/**
 * Clean up the route's URL
 * 
 * @param string $url
 * @return string
 */
function route_cleanup ($url) {
    return chop(preg_replace('/:(\w+)\??/', '', $url), '/ ');
};

/**
 * Execute order 66
 * 
 * @param string $url
 * @param string $method
 * @return array
 */
function fetch_route ($url, $method) {
    if (!$found = router_find($url, $method)) {
        return false;
    }
    
    emit('router:found', $found['found'], $found['matches']);
    
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
    
    $route = $found['found'];
    
    router('route', $found);
    
    if (!is_callable($route['action'])) {
        load_php($route['action']['file']);
    }
    
    return invoke_action($route, $found['matches']);
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
    $action = "action_{$action}";
    
    try {
        load_app_file("actions/$controller");
    }
    catch (Exception $e) {
        return false;
    }
    
    return invoke_action($action, $fragments);
}

/**
 * Invoke the route
 * 
 * @param array $action
 * @param array $parameters
 * @return mixed
 */
function invoke_action (array $route, array $parameters) {
    $action = $route['action'];
    $action = is_callable($action) ? $action : "action_{$action['name']}";
    
    if (is_string($action)) {
        if (function_exists('actions_init') && actions_init() === false) {
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
    $root = router('settings.root');
    $url  = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    
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
function get_baseurl ($base, $root) {
    $base   = trim($base, '/');
    $root   = trim($root, '/');
    $lenght = strlen($root);

    return $base === $root ? '' : trim(substr($base, $lenght), '/');
}

/**
 * Create a full link to route 
 * 
 * @param string $id
 * @param array $params
 * @param bool $absolute
 * @return string|bool
 */
function url ($id, $params = array(), $absolute = false) {
    if (!$route = router("routes.$id")) {
        return false;
    }
    
    $base = router('settings.base_url');    
    $root = router('settings.root');
    $root = $root ? $root : '';

    $basepath = $absolute ? "{$base}$root/" : chop("/$root", '/');
    
    $url = route_replace($route['url'], $params);
    
    return "$basepath/$url";
}

/**
 * Get URL path from root to file
 * 
 * @param string $path
 */
function path ($path = '') {
    $root = router('settings.root');
    $root = $root ? $root : '';
    
    $basepath = chop("/$root", '/'); 
    
    return "$basepath/$path";
}

/**
 * Redirect to route
 * 
 * @see url
 */
function redirect ($id, $params = array()) {
    redirect_path(url($id, $params));
}

/**
 * Redirect to URL relative to the website location
 * 
 * @see path
 */
function redirect_url ($url) {
    redirect_path(path($url));
}

/**
 * Redirect to path
 * 
 * @param string $path
 */
function redirect_path ($path) {
    header("Location: $path") xor exit;
}