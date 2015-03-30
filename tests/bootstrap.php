<?php

/**
 * PHPUnit testing bootstrap
 */

define('FFF_START'   , microtime(true));
define('FFF_BASEPATH', dirname(__DIR__) . '/');
define('FFF_APP_DIR' , FFF_BASEPATH . 'app/');
define('FFF_DEBUG'   , true);

require 'vendor/autoload.php';

$config = config('app/config');

system_load($config);