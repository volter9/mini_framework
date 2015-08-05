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
    
    $args = array_slice(func_get_args(), 1);
    $result = array();
    
    foreach ($event as $callback) {
        $result[] = call_user_func_array($callback, $args);
    }
    
    return $result;
}