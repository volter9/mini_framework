<?php namespace i18n;

use loader;
use storage;

/**
 * Internatinalization (aka i18n) functions
 * 
 * @package mini_framework
 */

function init (array $data) {
    storage('settings', $data);
}

/**
 * Language storage
 * 
 * @param mixed $key
 * @param mixed $value
 * @return mixed
 */
function storage ($key = null, $value = null) {
    static $repo = null;
    
    $repo or $repo = storage\repo();
    
    return $repo($key, $value);
}

/**
 * Load a language file
 * 
 * @param string $lang
 * @param string $path
 */
function load_language ($lang, $path) {
    $default = lang('settings.default');
    
    lang($lang, loader\php("$path/$default"));
    
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
function get ($string) {
    $language = lang('current');
    
    return lang("$language.$string");
}