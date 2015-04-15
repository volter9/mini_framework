<?php

/**
 * Language repo
 * 
 * @param mixed $key
 * @param mixed $value
 * @return mixed
 */
function lang ($key = null, $value = null) {
	static $repo = null;
	
	$repo or $repo = repo();
	
	return $repo($key, $value);
}

/**
 * Load a language file
 * 
 * @param string $lang
 * @param string $path
 */
function load_language ($lang, $path) {
	lang($lang, load_php($path));
	
	if (!lang('current')) {
        lang('current', $lang);
	}
}

/**
 * Get string from current loaded language
 * 
 * @param string $string
 * @return string
 */
function i18n ($string) {
	$language = lang('current');
	
	return lang("$language.$string");
}