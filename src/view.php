<?php

/**
 * View component
 * 
 * @package mini_framework
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
 * View a layout
 * 
 * @param string $view
 * @param array $data
 */
function layout ($view, array $data = array()) {
    $data['view'] = $view;
    
    if (!empty($data)) {
        views('data', $data);
    }
    
    render(view_path(views('templates.layout')), $data);
}

/**
 * View a view
 * 
 * @param string $view
 * @param array $data
 * @param bool $global
 */
function view ($view, $data = array(), $global = true) {
    if ($global) {
        views('data', $data);
    }
    
    render(view_path($view), empty($data) ? views('data') : $data);
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
 * Get template name from 
 * 
 * @param string $template
 * @return array
 */
function parse_template ($template) {
    $contains = contains($template, ':');
    
    return array( 
        $contains ? before($template, ':') : views('templates.template'),
        $contains ? after($template, ':') : $template
    );
}

/**
 * Transforms a template name to full template path
 * 
 * @param string $template
 * @return string
 */
function view_path ($view) {
    $directory = chop(views('templates.directory'), '/');
    
    list($template, $view) = parse_template($view);
    
    return "$directory/$template/html/$view.php";
}

/**
 * URL to template file
 * 
 * @param string $file
 * @return string
 */
function asset_url ($file = '') {
    list($template, $file) = parse_template($file);
    
    $folder = chop(views('templates.directory'), '/');
    $folder = after($folder, '/');
    
    $root = router('settings.root');
    
    return deduplicate("/$root/$folder/$template/$file", '/');
}

/**
 * Path to template file
 * 
 * @param string $file
 * @return string
 */
function asset_path ($file = '') {
    list($template, $file) = parse_template($file);
    $directory = views('templates.directory');
    
    return "$directory/$template/$file";
}