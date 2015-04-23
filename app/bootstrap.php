<?php

/**
 * Bootstrap hook.
 * 
 * Anything related to custom intialization or PHP system 
 * tweaks goes here. Here's custom handler of exceptions and
 * error reporting to show all errors and notices.
 * 
 * Anything related to bootstrap can be included here.
 */

ob_start();

ini_set('display_errors', (int)defined('MF_DEBUG'));
error_reporting(-(int)defined('MF_DEBUG'));

date_default_timezone_set('Europe/Moscow');
mb_internal_encoding('UTF-8');

set_exception_handler(function ($e) {
    !defined('MF_DEBUG') or show_error($e);
});

/**
 * Debug information for 404 page
 * 
 * I think I need to create some kind of bar to show all that 
 * debug information. Yeah, mini_bar, do you like the name? I do!
 */
defined('MF_DEBUG') and bind('router:not_found', function () {
    $bool2str = function ($value) {
        return $value ? 'true' : 'false';
    };
    
    $route    = $bool2str(router('route'));
    $file     = $bool2str(file_exists(router('route.route.action') . '.php'));
    $function = $bool2str(function_exists('actions_init'));
    $action   = $bool2str(function_exists('action_' . router('route.route.name')));
    
    echo "<!-- Route found: $route, ";
    echo "File found: $file, ";
    echo "Function `actions_init()` found: $function, ";
    echo "Action found: $action -->\n";
});