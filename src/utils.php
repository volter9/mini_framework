<?php

/**
 * FFFramework utilities
 * 
 * @package FFFramework
 */

/**
 * Storage container with getter and setter abilities
 * 
 * @param array $default
 * @param bool $readonly
 * @return callable
 */
function repo ($default = array(), $readonly = false) {
	$repo = $default;
	
	/**
	 * Repository callback itself
	 * 
	 * @param string $key
	 * @param mixed $value
	 * @return mixed
	 */
	return function ($key = null, $value = null) use ($readonly, &$repo) {
		// Setters
		if (!$readonly && $key !== null && $value !== null) {
			if (strpos($key, '.') !== false) {
				return array_set($repo, $key, $value);
			}
			else {
				if (
					is_array($value) && 
					isset($repo[$key]) && 
					is_array($repo[$key])
				) {
					$repo[$key] = array_merge($repo[$key], $value);
				}
				else {
					$repo[$key] = $value;
				}
			}
			
			return;
		}
		else if (is_array($key)) {
			$repo = array_merge($repo, $key);
			
			return;
		}
		
		// Getters
		if (strpos($key, '.') !== false) {
			return array_get($repo, $key);
		}
		else if ( isset($repo[$key]) ) {
			return $repo[$key];
		}
		else if ($key) {
			return false;
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
		
		if ( isset($repo[$key]) ) {
			return $repo[$key];
		}
		else {
			return false;
		}
	};
}

/**
 * Exclude string from string
 * 
 * @param string $delimiter
 * @param string $subject
 * @return string
 */
function exclude ($delimiter, $subject) {
	return implode('', explode($delimiter, $subject));
}

/**
 * Pluck an array from other array of specific key
 * 
 * @param array $array
 * @param string $field
 * @param mixed $default
 * @return array 
 */
function pluck (array $array, $field, $default = '') {
	$result = array();
	
	foreach ($array as $key => $value) {
		$result[$key] = isset($value[$field]) ? $value[$field] : $default;
	}
	
	return $result;
}