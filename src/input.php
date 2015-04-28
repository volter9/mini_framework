<?php

/**
 * Set/get session
 * 
 * @param string $key
 * @param mixed $value
 * @return mixed
 */
function session ($key = null, $value = null) {
    if ($key && $value !== null) {
        $_SESSION[$key] = $value;
    }
    
    if ($key && $value === false) {
        unset($_SESSION[$key]);
    }
    
    if ($key && !$value && isset($_SESSION[$key])) {
        return $_SESSION[$key];
    }
    
    return $_SESSION;
}

/**
 * Get input depending on method
 * 
 * @param string $key
 */
function input ($key = null, $sanitize = false) {
    $array = get_array();
    $value = false;
    
    if ($key && isset($array[$key])) {
        $value = $array[$key];
    }
    else if ($key === null) {
        $value = $array;
    }
    
    return $sanitize ? sanitize($value) : $value;
}

/**
 * Get a method array
 * 
 * @return array
 */
function get_array () {
    $method = strtoupper($_SERVER['REQUEST_METHOD']);
    
    switch ($method) {
        case 'GET':
            return $_GET;
        
        case 'POST':
            return $_POST;
        
        /**
         * Other methods available only in **enterprise edition**.
         * But here's an empty array for you, as a fallback.
         */
        default:
            return array();
    }
}

/**
 * Is request method is post
 * 
 * @return bool
 */
function is_post () {
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

/**
 * Is request is AJAX request
 * 
 * @return bool
 */
function is_ajax () {
    return strtolower(array_get($_SERVER, 'HTTP_X_REQUEST_WITH')) === 'xmlhttprequest';
}

/**
 * Sanitize input (recursive)
 * 
 * @param mixed $input
 * @return mixed
 */
function sanitize ($input) {
    if (is_array($input)) {
        return array_map(function ($v) {
            return sanitize($v);
        }, $input);
    }
    
    return filter_var($input, FILTER_SANITIZE_STRING);
}