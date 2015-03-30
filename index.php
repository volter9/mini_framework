<?php

/**
 * FFFramework simple framework in procedural/functional PHP, 
 *
 * @author volter9
 */

/**
 * FFFramework constants
 * 
 * @const string FFF_VERSION Version of mini_blog
 * @const string FFF_BASEPATH Base path of the app
 * @const string FFF_APP_DIR Path of app dir (app/)
 */
define('FFF_BASEPATH', __DIR__ . '/');
define('FFF_APP_DIR' , FFF_BASEPATH . 'app/');
define('FFF_DEBUG'   , true);

$time = microtime(true);

/** App */
require 'vendor/autoload.php';

/** Boot the app */
app_boot(sprintf('%sconfig', FFF_APP_DIR));

/** Showing debug information */
defined('FFF_DEBUG') and printf('<!-- Execution time: %.5f -->', microtime(true) - $time);