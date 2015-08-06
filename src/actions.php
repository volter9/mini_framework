<?php namespace actions;

use loader;

/**
 * Controller callback
 * 
 * @param string $function
 * @param string $path
 * @return \Closure
 */
function controller ($function, $namespace, $path) {
    return function () use ($function, $namespace, $path) {
        loader\php($path);
        
        function_exists($fn = "$namespace\\init") and $fn();
        
        return call_user_func_array("$namespace\\$function", func_get_args());
    };
}

/**
 * Callback dispatcher
 * 
 * @param string $namespace
 * @param string $path
 */
function dispatcher ($namespace, $path) {
    return function ($fragments = '') use ($namespace, $path) {
        $fragments = explode('/', $fragments);
        
        $action = array_shift($fragments);
        $action = $action ? $action : 'index';
        
        loader\php($path);
        
        function_exists($fn = "$namespace\\init") and $fn();
        
        return call_user_func_array("$namespace\\$action", $fragments);
    };
}

/**
 * Lazy loading function action
 * 
 * @param string $function 
 * @param string $path
 * @return \Closure
 */
function fn ($function, $path) {
    return function () use ($function, $path) {
        loader\php($path);
        
        return call_user_func_array($function, func_get_args());
    };
}