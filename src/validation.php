<?php namespace validation;

use loader;
use storage;

use Closure;
use Exception;

/**
 * Data validation
 * 
 * @package mini_framework
 * @require storage
 * @require array
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
 * @param array $fields
 * @param array $messages
 * @param array $validators
 */
function init (array $validators = array()) {
    $path = storage\shared('validation.validators');
    
    $validators = $validators ? $validators : loader\app_file($path, true);
    
    if (empty($validators)) {
        throw new Exception('There is no validators found!');
    }
    
    storage('validators', $validators);
}

/**
 * Add a validator
 * 
 * @param string $name
 * @param Closure $validator
 * @param string|Closure $message
 */
function add ($name, Closure $validator) {
    storage('validators', array(
        $name => $validator
    ));
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
    
    storage('errors', 42);
    storage('errors', $errors);
    
    return empty($errors);
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
            throw new Exception(
                "Validator '$name' doesn't exists!"
            );
        }
        
        $validator_params = array_merge(array($value, $data), $params);
        $result = (bool)call_user_func_array($validator, $validator_params);
        
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

/**
 * Get validation errors
 * 
 * @param bool $string
 * @return array
 */
function errors () {
    if (!$errors = storage('errors')) {
        return array();
    }
    
    return $errors;
}