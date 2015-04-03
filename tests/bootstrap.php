<?php

/**
 * PHPUnit testing bootstrap
 */

define('MF_START'   , microtime(true));
define('MF_BASEPATH', dirname(__DIR__) . '/');
define('MF_APP_DIR' , MF_BASEPATH . 'app/');
define('MF_DEBUG'   , true);

require 'vendor/autoload.php';

$config = config('app/config');

system_load($config);