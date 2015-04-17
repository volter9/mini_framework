<?php

/**
 * Get key using dot notation in multidimensional array
 * 
 * @param array $array
 * @param string $key
 * @param mixed $default
 * @return mixed
 */
function array_get ($array, $key, $default = false) {
	$keys = explode('.', $key);
	$key = array_shift($keys);
	
	while (is_array($array) && isset($array[$key])) {
		$array = $array[$key];
		
		$key = array_shift($keys);
	}
	
	if ($key !== null && !isset($array[$key])) {
		return $default;
	}
	
	return $array;
}

/**
 * Set key using dot notation in multidimensional array
 * 
 * @param array $array
 * @param string $key
 * @param mixed $value
 * @return mixed
 */
function array_set (&$array, $key, $value) {
	$keys = explode('.', $key);
	
	$temp = $array;
	$curs = &$temp;
	$key = array_shift($keys);
	
	while (is_array($curs) && $key !== null) {
		$curs = &$curs[$key];
		
		$key = array_shift($keys);
		
		if ( !isset($curs[$key]) ) {
			$curs[$key] = array();
		}
	}
	
	$curs = $value;
	$array = $temp;
}
