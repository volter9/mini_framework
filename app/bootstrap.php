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
session_start();

ini_set('display_errors', (int)defined('MF_DEBUG'));
error_reporting(-(int)defined('MF_DEBUG'));

date_default_timezone_set('Europe/Moscow');
mb_internal_encoding('UTF-8');

set_exception_handler(function ($e) {
	!defined('MF_DEBUG') or show_error($e);
});