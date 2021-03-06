<?php

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
function validation ($key = null, $value = null) {
    static $repo;
    $repo or $repo = repo();
    
    return $repo($key, $value);
}

/**
 * Initiate validation
 * 
 * @param array $fields
 * @param array $messages
 * @param array $validators
 */
function validation_init (array $fields, array $messages, array $validators = array()) {
    $path = storage('validation.validators');
    
    $validators = $validators ? $validators : load_app_file($path, true);
    
    if (empty($validators)) {
        throw new Exception('There is no validators found!');
    }
    
    validators($validators);
    validation_fields($fields);
    validators_messages($messages);
}

/**
 * Set validators callbacks
 * 
 * @param array $validators
 */
function validators (array $validators) {
    validation('validators', $validators);
}

/**
 * Set validators error messages
 * 
 * @param array $messages
 */
function validators_messages (array $messages) {
    validation('messages', $messages);
}

/**
 * Set validation fields
 * 
 * @param array $fields
 */
function validation_fields (array $fields) {
    validation('fields', $fields);
}

/**
 * Add a validator
 * 
 * @param string $name
 * @param Closure $validator
 * @param string|Closure $message
 */
function add_validator ($name, Closure $validator, $message) {
    validators(array(
        $name => $validator
    ));
    
    validators_messages(array(
        $name => $message
    ));
}

/**
 * Validate input data
 * 
 * @param array $data
 * @param array $rules
 * @return bool
 */
function validate (array $data, array $rules) {
    if (!$rules) {
        throw new Exception(
            'Validation rules were not initialized!'
        );
    }
    
    $errors = array();
    
    foreach ($rules as $field => $set) {
        $value = array_get($data, $field, null);
        $error = validate_field($field, $value, $set, $data);
        
        if (is_string($error)) {
            $errors[$field] = $error;
        }
    }
    
    validation('errors', $errors);
    
    return empty($errors);
}

/**
 * Validate a field
 * 
 * @todo This function is **too** long
 *       You need to break down it into smaller functions
 * @param string $field
 * @param mixed $value
 * @param string $rules
 * @param array $data
 * @return bool|array
 */
function validate_field ($field, $value, $rules, array $data) {
    $error = null;
    
    foreach (parse_rules($rules) as $rule) {
        $name      = $rule['validator'];
        $params    = $rule['params'];
        $validator = validation("validators.$name");
        
        if (!$validator) {
            throw new Exception(
                "Validator '$name' doesn't exists!"
            );
        }
        
        $validator_params = array_merge(array($value, $data), $params);
        $result = (bool)call_user_func_array($validator, $validator_params);
        
        if ($result) {
            continue;
        }
        
        $message = validation("messages.$name");
        $string  = is_string($message);
        
        array_unshift($params, validation("fields.$field"));
        
        if ($string) {
            array_unshift($params, $message);
        }
        
        $error = call_user_func_array($string ? 'sprintf' : $message, $params);
        
        break;
    }
    
    return $error;
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
        
        $result[] = array(
            'validator' => $validator,
            'params'    => $params
        );
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
 * @return array|string
 */
function validation_errors ($string = false) {
    if (!$errors = validation('errors')) {
        return array();
    }
    
    return $string ? implode(' ', $errors) : $errors;
}