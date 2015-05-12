<?php

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
function forms ($key = null, $value = null) {
    static $repo;
    
    $repo or $repo = repo(array(
        'providers' => array(),
        'elements'  => array()
    ));
    
    return $repo($key, $value);
}

/**
 * Add a form provider
 * 
 * @param string $name
 * @param Closure $callback
 */
function form_provider ($name, Closure $callback) {
    forms('providers', array($name => $callback));
}

/**
 * Build a form
 * 
 * @param array $scheme
 * @param array $data
 */
function build_form (array $scheme, array $data) {
    $view = $scheme['view'];
    
    view($view, array(
        'scheme' => $scheme,
        'data' => $data
    ));
}

/**
 * Build an element
 * 
 * @param array $data
 */
function build_element ($type, array $data) {
    if (strpos($type, ':') !== false) {
        list($type, $provider) = explode(':', $type);
        
        return build_element_provider($type, $provider, $data);
    }
    
    view("forms/elements/$type", $data);
}

/**
 * Build an element with a data provided by a data provider
 * 
 * @param string $type
 * @param string $provider
 * @param array $data
 */
function build_element_provider ($type, $provider, array $data) {
    $data_provider = forms("providers.$provider");
    
    if (!$data_provider) {
        throw new Exception(
            "Provider '$provider' doesn't exists!"
        );
    }
    
    view("forms/elements/$type", array_merge($data, array(
        'data' => $data_provider()
    )));
}