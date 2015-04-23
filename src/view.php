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
function asset_path ($file = '') {
    $template = views('templates.template');
    $folder = templates_folder();
    $root = router('settings.root');
    
    return '/' . trim("/$root/$folder/$template/$file", '/');
}

/**
 * Get templates folder
 * 
 * @return string
 */
function templates_folder () {
    $directory = chop(views('templates.directory'), '/');
    
    return substr($directory, strrpos($directory, '/') + 1);
}