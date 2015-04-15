<?php

/**
 * Data validation
 * 
 * @package FFFramework
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
 * Set validation rules
 * 
 * @param array $rules
 */
function validation_rules (array $rules) {
	validation('rules', $rules);
}

/**
 * Set validators callbacks
 * 
 * @param aray $validators
 */
function validators (array $validators) {
	validation('validators', $validators);
}

/**
 * Set validators error messages
 * 
 * @param aray $messages
 */
function validators_messages (array $messages) {
	validation('messages', $messages);
}

/**
 * Set validation fields
 * 
 * @param aray $fields
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
 * Setup validation
 * 
 * @param Closure $config
 */
function validation_init (array $validators, array $messages) {
	validators($validators);
	validators_messages($messages);
}

/**
 * Validate input data
 * 
 * @param array $data
 * @return bool
 */
function validate (array $data) {
	if ( !($rules = validation('rules')) ) {
		throw new Exception(
			'Validation rules were not initialized!'
		);
	}
	
	$errors = array();
	
	foreach ($rules as $field => $set) {
		$value = isset($data[$field]) ? $data[$field] : null;
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
 * @param string $field
 * @param mixed $value
 * @param array $rules
 * @return bool|array
 */
function validate_field ($field, $value, $rules, array $data) {
	$errors = null;
	
	foreach (parse_rules($rules) as $rule) {
		$name      = $rule['validator'];
		$params    = $rule['params'];
		$validator = validation("validators.$name");
		
		if (!$validator) {
		    throw new Exception(
		        "Validator '$name' doesn't exists!"
		    );
		}
		
		$result = call_user_func_array($validator, array_merge(array($value, $data), $params));
		
		if (!(bool)$result) {
			$message = validation("messages.$name");
			
			if (is_string($message)) {
				array_unshift($params, $message, validation("fields.$field"));
				
				$message = call_user_func_array('sprintf', $params);
			}
			else {
				array_unshift($params, validation("fields.$field"));
				
				$message = call_user_func_array($message, $params);
			}
			
			$errors = $message;
			
			break;
		}
	}
	
	return $errors;
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
			'params' => $params
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
	
	$params = array_map(
        function ($v) {
            return is_numeric($v) ? (int)$v : $v;
        }, 
        explode(',', $params)
    );
	
	return array($validator, $params);
}

/**
 * Get validation errors
 * 
 * @param bool $each
 * @param bool $string
 * @return array|string
 */
function validation_errors ($string = false) {
	if (!($errors = validation('errors'))) {
		return false;
	}
	
	return $string ? implode(' ', $errors) : $errors;
}