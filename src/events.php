<?php namespace events;

use storage;

/**
 * Events functions
 * 
 * @package mini_framework
 * @require storage
 */

/**
 * Events storage
 * 
 * @param string $event
 * @param callable $callback
 */
function storage ($event, $callback = null) {
    static $stack = null;
    $stack or $stack = storage\stack();
    
    return $stack($event, $callback);
}

/**
 * Bind an event
 * 
 * @param string $event
 * @param callable $callback
 */
function bind ($event, $callback) {
    storage($event, $callback);
}

/**
 * Emit an event
 * 
 * @param string $event
 * @return array
 */
function emit ($event) {
    $args  = array_slice(func_get_args(), 1);
    $event = storage($event);
    
    if (empty($event)) {
        return false;
    }
    
    $result = array();
    
    foreach ($event as $callback) {
        if ($value = call_user_func_array($callback, $args)) {
            $result[] = $value;
        }
    }
    
    return $result;
}