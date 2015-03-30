<?php

/**
 * View component
 * 
 * @package FFFramework
 */

/**
 * Views repository
 * 
 * @param string $key
 * @param mixed $value
 * @return mixed
 */
function views ($key = null, $value = null) {
	static $repo = null;
	$repo or $repo = repo();
	
	return $repo($key, $value);
}

/**
 * View a view
 * 
 * @param string $template
 * @param array $data
 */
function view ($template, $data = [], $global = true) {
	$template = view_path($template);
	
	if (!empty($data) && $global) {
		views('data', $data);
	}
	
	$global_data = views('data');
	
	render($template, $global && $global_data ? $global_data : $data);
}

/**
 * Isolation function from view's function context
 * 
 * @param string $__view__
 * @param array $__data__
 */
function render ($__view__, array $__data__) {
    extract($__data__);
    
    require($__view__);
}

/**
 * Transforms a template name to full template path
 * 
 * @param string $template
 * @return string
 */
function view_path ($template) {
	static $views = null;
	
	if ($views === null) {
		$views = views('templates');
	}
	
	$skin = $views['template'];
	
	if (strpos($template, ':') !== false) {
		list($skin, $template) = explode(':', $template);
	}
	
	return "{$views['directory']}{$skin}/html/$template.php";
}

/**
 * Path to template file
 * 
 * @param string $file
 */
function template_path ($file = '') {
	static $settings = null;
	
	$settings or $settings = router('settings');
	$template = views('templates.template');
	
	return '/' . trim("/{$settings['root']}/templates/$template/$file", '/');
}