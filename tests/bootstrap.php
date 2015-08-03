<?php

/** 
 * Require composer autoloader and PHPUnit test case alias 
 */
require 'vendor/autoload.php';
require 'tests/TestCase.php';

/**
 * Define default mini_framework constants
 */
define('MF_BASEPATH', __DIR__ . '/');
define('MF_APP_DIR' , __DIR__ . '/');

/**
 * Oh, global state manipulation
 */
$_SERVER['DOCUMENT_ROOT'] = __DIR__ . '/';

app\system_load(storage\config('tests/resources/config'));

validation\init();

db\connect();
db\query(file_get_contents(app\base_path('resources/dump.sql')));

/**
 * PHP system tweaks
 */
session_start();
error_reporting(E_ALL | E_STRICT);
date_default_timezone_set('UTC');

mb_language('uni');
mb_regex_encoding('UTF-8');
mb_internal_encoding('UTF-8');