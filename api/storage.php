<?php

/**
 * Storage
 * 
 * @package FFFramework
 */

/**
 * Global storage
 * In this function you can store any information you want
 * 
 * @param string $key
 * @param mixed $value
 * @return mixed
 */
function storage ($key = null, $value = null) {
	static $repo = null;
	$repo or $repo = repo();
	
	return $repo($key, $value);
}
