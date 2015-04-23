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

/**
 * Extract array elements by specified keys
 * 
 * @param array $array
 * @param array $keys
 * @return array
 */
function array_extract (array $array, array $keys) {
    $result = array();
    
    foreach ($keys as $key) {
        if (isset($array[$key])) {
            $result[$key] = $array[$key];
        }
    }
    
    return $result;
}

/**
 * Remove keys specified in key array
 * 
 * @param array $array
 * @param array $keys
 * @return array
 */
function array_exclude (array $array, array $keys) {
    foreach ($keys as $key) {
        unset($array[$key]);
    }
    
    return $array;
}

/**
 * Get key value from multidimensional array one level deep
 * 
 * @param array $array
 * @param string $key
 * @return array
 */
function array_pluck (array $array, $key) {
    $result = array();
    
    foreach ($array as $subarray) {
        if (isset($subarray[$key])) {
            $result[] = $subarray[$key];
        }
    }
    
    return $result;
}

/**
 * Works as array_pluck, but you can specify not only value key,
 * but also a key key
 * 
 * @param array $array
 * @param string $key
 * @param string $value
 */
function array_join (array $array, $key, $value) {
    return array_combine(
        array_pluck($array, $key),
        array_pluck($array, $value)
    );
}