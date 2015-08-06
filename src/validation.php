<?php namespace validation;

use loader;
use storage;

use Exception;

/**
 * Data validation
 * 
 * @package mini_framework
 */

/**
 * Validation container
 * 
 * @param mixed $key
 * @param mixed $value
 * @return mixed
 */
function storage ($key = null, $value = null) {
    static $repo;
    
    $repo or $repo = storage\repo();
    
    return $repo($key, $value);
}

/**
 * Initiate validation
 * 
 * @param array $data
 */
function init (array $data) {
    $validators = array_get($data, 'validators');
    
    if (!$validators) {
        return;
    }
    
    $validators = loader\app_file($validators, true);
    
    if (empty($validators)) {
        throw new Exception('No validators were found!');
    }
    
    storage('validators', $validators);
}

/**
 * Add a validator
 * 
 * @param string $name
 * @param callable $validator
 */
function add ($name, $validator) {
    storage('validators', array($name => $validator));
}

/**
 * Validate input data
 * 
 * @param array $data
 * @param array $rules
 * @param bool $halt
 * @return bool
 */
function validate (array $data, array $rules, $halt = false) {
    return empty(validate_errors($data, $rules, $halt));
}

/**
 * Validate input data and return errors
 * 
 * @param array $data
 * @param array $rules
 * @param bool $halt
 * @return array
 */
function validate_errors (array $data, array $rules, $halt = false) {
    if (!$rules) {
        throw new Exception('Validation rules were not initialized!');
    }
    
    $errors = array();
    
    foreach ($rules as $field => $rules) {
        $value = array_get($data, $field, null);
        $error = validate_field($rules, $value, $data);
        
        if ($error !== true) {
            $errors[$field] = $error;
            
            if ($halt) {
                break;
            }
        }
    }
    
    return $errors;
}

/**
 * Validate a field
 * 
 * @todo This function is **too** long
 *       You need to break down it into smaller functions
 * @param string $rules
 * @param mixed $value
 * @param array $data
 * @return bool|array
 */
function validate_field ($rules, $value, array $data = array()) {
    foreach (parse_rules($rules) as $name => $params) {
        $validator = storage("validators.$name");
        
        if (!$validator) {
            throw new Exception("Validator '$name' doesn't exists!");
        }
        
        $args = array_merge(array($value, $data), $params);
        $result = call_user_func_array($validator, $args);
        
        if (!$result) {
            return array($name, $params);
        }
    }
    
    return true;
}

/**
 * Parse rule set
 * 
 * @param string $rules
 * @return array
 */
function parse_rules ($rules) {
    $rules = explode('|', $rules);
    $result = array();
    
    foreach ($rules as $validator) {
        $params = array();
        
        if (strpos($validator, ':') !== false) {
            list($validator, $params) = parse_rule($validator);
        }
        
        $result[$validator] = $params;
    }
    
    return $result;
}

/**
 * Parse a rule
 * 
 * @param string $rule
 * @return array
 */
function parse_rule ($rule) {
    list($validator, $params) = explode(':', $rule);
    
    return array($validator, array_numerify(explode(',', $params)));
}