<?php

/**
 * Storage
 * 
 * @package mini_framework
 */

/**
 * Storage container with getter and setter abilities
 * 
 * @param array $default
 * @param bool $readonly
 * @return callable
 */
function repo (array $default = array(), $readonly = false) {
    $repo = $default;
    
    /**
     * Repository callback itself
     * 
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    return function ($key = null, $value = null) use ($readonly, &$repo) {
        if (!$readonly && $key !== null && $value !== null) {
            array_set($repo, $key, $value);
            
            return;
        }
        
        if (!$readonly && is_array($key)) {
            $repo = array_merge($repo, $key);
            
            return;
        }
        
        if ($key) {
            return array_get($repo, $key);
        }
        
        return $repo;
    };
}

/**
 * Stack storage
 * 
 * @return callable
 */
function stack () {
    $repo = array();
    
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
        else if ($key && $value === false) {
            unset($repo[$key]);
            return;
        }
        
        if (isset($repo[$key])) {
            return $repo[$key];
        }
        else {
            return false;
        }
    };
}

/**
 * Creates a PHP config
 * 
 * @param string $config
 * @return callable
 */
function config ($config) { 
    return repo(load_php($config, true), true);
}

/**
 * Global storage
 * 
 * In this function you can store any information you want
 * 
 * @param string $key
 * @param mixed $value
 * @return mixed
 */
function storage ($key = null, $value = null) {
    static $repo = null;
    $repo or $repo = repo();
    
    return $repo($key, $value);
}
