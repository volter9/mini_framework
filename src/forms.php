<?php namespace forms;

use storage;
use view;

/**
 * Form building function
 * 
 * @package mini_blog
 */

/**
 * Forms storage
 * 
 * @param mixed $key
 * @param mixed $value
 * @return mixed
 */
function storage ($key = null, $value = null) {
    static $repo;
    
    $repo or $repo = storage\repo(array(
        'providers' => array()
    ));
    
    return $repo($key, $value);
}

/**
 * Add a form provider
 * 
 * @param string $name
 * @param callable $callback
 */
function provider ($name, $callback) {
    storage('providers', array($name => $callback));
}

/**
 * Build a form
 * 
 * @param array $scheme
 * @param array $data
 */
function build (array $scheme, array $data) {
    $view = $scheme['view'];
    
    view\partial($view, array(
        'scheme' => $scheme,
        'data'   => $data
    ));
}

/**
 * Path to form element
 * 
 * @param string $type
 * @return string
 */
function element_path ($type) {
    return starts_with($type, '/') ? $type : "forms/elements/$type";
}

/**
 * Build an element
 * 
 * @param array $data
 */
function element ($type, array $data) {
    if (contains($type, ':')) {
        list($type, $provider) = explode(':', $type);
        
        return element_provider($type, $provider, $data);
    }
    
    view\partial(element_path($type), $data);
}

/**
 * Build an element with a data provided by a data provider
 * 
 * @param string $type
 * @param string $provider
 * @param array $data
 */
function element_provider ($type, $provider, array $data) {
    $data_provider = storage("providers.$provider");
    
    if (!$data_provider) {
        throw new Exception("Provider '$provider' doesn't exists!");
    }
    
    view\partial(element_path($type), array_merge($data, array(
        'data' => $data_provider()
    )));
}