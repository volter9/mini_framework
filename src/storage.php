<?php namespace storage;

use loader;

/**
 * Storage closures
 * 
 * @package mini_framework
 * @requrie array
 * @require loader
 */

/**
 * Storage container with getter and setter abilities
 * 
 * @param array $repo
 * @param bool $readonly
 * @return \Closure
 */
function repo (array $repo = array(), $readonly = false) {
    /**
     * Repository callback itself
     * 
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    return function ($key = null, $value = null) use ($readonly, &$repo) {
        if (!$readonly) {
            if ($key !== null && $value !== null) {
                return array_set($repo, $key, $value);
            }
        
            if (is_array($key)) {
                $repo = array_merge($repo, $key);
            
                return;
            }
        }
        
        return $key ? array_get($repo, $key) : $repo;
    };
}

/**
 * Stack storage
 * 
 * @param array $repo
 * @return \Closure
 */
function stack (array $repo = array()) {
    /**
     * Stack callback
     * 
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    return function ($key = null, $value = null) use (&$repo) {
        if ($key && $value) {
            $repo[$key][] = $value;
            
            return;
        }
        
        if ($key && $value === false) {
            unset($repo[$key]);
            
            return;
        }
        
        return isset($repo[$key]) ? $repo[$key] : false;
    };
}

/**
 * Simple key-value storage
 * 
 * @param array $repo
 */
function box (array $repo = array()) {
    /**
     * Repository callback
     * 
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    return function ($key, $value = null) use (&$repo) {
        if ($key !== null && $value !== null) {
            $repo[$key] = $value;
            
            return;
        }
        
        return isset($repo[$key]) ? $repo[$key] : false;
    };
}

/**
 * Creates a PHP config
 * 
 * @param string $file
 * @return callable
 */
function config ($file) { 
    return repo(loader\php($file, true), true);
}

/**
 * Global shared storage
 * 
 * In this function you can store any information you want
 * 
 * @param string $key
 * @param mixed $value
 * @return mixed
 */
function shared ($key = null, $value = null) {
    static $repo = null;
    
    $repo or $repo = repo();
    
    return $repo($key, $value);
}
