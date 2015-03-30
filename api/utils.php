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
function repo ($default = [], $readonly = false) {
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
				return md_set($repo, $key, $value);
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
			return md_get($repo, $key);
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
 * Get key using dot notation in multidimensional array
 * 
 * @link https://gist.github.com/Volter9/e8568303a09716e72039
 * @param array $array
 * @param string $key
 * @return mixed
 */
function md_get ($array, $key) {
	$keys = explode('.', $key);
	$key = array_shift($keys);
	
	while ( is_array($array) && isset($array[$key]) ) {
		$array = $array[$key];
		
		$key = array_shift($keys);
	}
	
	if ($key !== null && !isset($array[$key])) {
		return false;
	}
	
	return $array;
}

/**
 * Set key using dot notation in multidimensional array
 * 
 * @link https://gist.github.com/Volter9/e8568303a09716e72039
 * @param array $array
 * @param string $key
 * @param mixed $value
 * @return mixed
 */
function md_set (&$array, $key, $value) {
	$keys = explode('.', $key);
	
	$temp = $array;
	$curs = &$temp;
	$key = array_shift($keys);
	
	while ( is_array($curs) && $key !== null ) {
		$curs = &$curs[$key];
		
		$key = array_shift($keys);
		
		if ( !isset($curs[$key]) ) {
			$curs[$key] = [];
		}
	}
	
	$curs = $value;
	$array = $temp;
}

/**
 * Stack storage
 * 
 * @return callable
 */
function stack () {
	$repo = [];
	
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
	$result = [];
	
	foreach ($array as $key => $value) {
		$result[$key] = isset($value[$field]) ? $value[$field] : $default;
	}
	
	return $result;
}