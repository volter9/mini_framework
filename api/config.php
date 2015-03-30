<?php

/**
 * Creates a PHP config
 * 
 * @param string $config
 * @return callable
 */
function config ($config) {	
	return repo(load_php($config, true), true);
}