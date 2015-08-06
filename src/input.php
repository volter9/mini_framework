<?php namespace input;

/**
 * Input functions (session and get/post values)
 * 
 * @package mini_framework
 */

/**
 * Set/get session
 * 
 * @param string $key
 * @param mixed $value
 * @return mixed
 */
function session ($key = null, $value = null) {
    if ($key) {
        if ($value !== null) {
            array_set($_SESSION, $key, $value);
        }
        else if ($value === false) {
            unset($_SESSION[$key]);
        }
        else {
            return array_get($_SESSION, $key);
        }
    }
    
    return $_SESSION;
}

/**
 * Get input depending on method
 * 
 * @param string $key
 * @param bool $sanitize
 * @return array
 */
function get ($key = null, $sanitize = false) {
    $value = get_array();
    
    if ($key && isset($value[$key])) {
        $value = $value[$key];
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
        
        case 'PUT':
            return parse_str(file_get_contents('php://input'));
        
        /**
         * Other methods available only in **enterprise edition**.
         * An empty array is provided as a fallback.
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
    return strtoupper($_SERVER['REQUEST_METHOD']) === 'POST';
}

/**
 * Is request is AJAX request.
 * jQuery AJAX or with set X-Requested-With header
 * 
 * @return bool
 */
function is_ajax () {
    return strtolower(array_get($_SERVER, 'HTTP_X_REQUESTED_WITH')) === 'xmlhttprequest';
}

/**
 * Sanitize input (recursive)
 * 
 * @param mixed $input
 * @return mixed
 */
function sanitize ($input) {
    return is_array($input) 
        ? array_map('\input\sanitize', $input)
        : filter_var($input, FILTER_SANITIZE_STRING);
}