<?php namespace events;

use storage;

/**
 * Events functions
 * 
 * @package mini_framework
 */

/**
 * Events storage
 * 
 * @param string $event
 * @param callable $callback
 */
function bind ($event, $callback = null) {
    static $stack = null;
    
    $stack or $stack = storage\stack();
    
    return $stack($event, $callback);
}

/**
 * Emit an event
 * 
 * @param string $event
 * @return array
 */
function emit ($event) {
    if (!$event = bind($event)) {
        return false;
    }
    
    $result = array();
    $args   = array_slice(func_get_args(), 1);
    
    foreach ($event as $callback) {
        $result[] = call_user_func_array($callback, $args);
    }
    
    return $result;
}