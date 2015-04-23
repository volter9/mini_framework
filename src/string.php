<?php

/**
 * Check if string is starts with other string
 * 
 * @param string $haystack
 * @param string $needle
 * @return string
 */
function starts_with ($haystack, $needle) {
    return strpos($haystack, $needle) === 0;
}

/**
 * Check if string is starts with other string
 * 
 * @param string $haystack
 * @param string $needle
 * @return string
 */
function ends_with ($haystack, $needle) {
    return strpos($haystack, $needle) === strlen($haystack) - strlen($needle);
}

/**
 * Last occurence after needle
 * 
 * @param string $haystack
 * @param string $needle
 * @param bool $without_needle
 * @return string
 */
function last ($haystack, $needle, $without_needle = true) {
    return substr($haystack, strrpos($haystack, $needle) + $without_needle);
}

/**
 * Remove (deduplicate) reoccuring characters/strings in haystack.
 * Useful when you need to remove reoccuring slashes in URL.
 * 
 * @param string $haystack
 * @param string $needle
 * @return string
 */
function deduplicate ($haystack, $needle) {
    $regexp = "@(?:{$needle})+@i";
    
    return preg_replace($regexp, $needle, $haystack);
}

/**
 * Exclude string from string
 * 
 * @param string $haystack
 * @param string $needle
 * @return string
 */
function exclude ($haystack, $needle) {
    if ($needle === '') {
        return $haystack;
    }
    
    return implode('', explode($needle, $haystack));
}