<?php

/**
 * mini_framework is a simple framework in procedural/functional PHP5
 *
 * @todo write unit tests
 * @author volter9
 */

/**
 * mini_framework constants
 * 
 * @const string MF_VERSION Version of mini_blog
 * @const string MF_BASEPATH Base path of the app
 * @const string MF_APP_DIR Path of app dir (app/)
 */
define('MF_BASEPATH', __DIR__ . '/');
define('MF_APP_DIR' , __DIR__ . '/app/');
define('MF_DEBUG'   , true);

$time = microtime(true);

/** Require composer */
require 'vendor/autoload.php';

/** Boot the app */
app_boot(app_path('config'));

/** Show some debug information */
defined('MF_DEBUG') and printf('<!-- Execution time: %.5f -->', microtime(true) - $time);