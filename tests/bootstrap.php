<?php

require 'vendor/autoload.php';
require 'tests/TestCase.php';

define('MF_BASEPATH', __DIR__ . '/');
define('MF_APP_DIR' , __DIR__ . '/');

$_SERVER['DOCUMENT_ROOT'] = __DIR__ . '/';

$config = storage\config(app\base_path('resources/config'));

app\system_load($config);

validation\init(
    loader\app_file('resources/fields', true),
    loader\app_file('resources/messages', true)
);

db\connect();
db\query(
    file_get_contents(app\base_path('resources/dump.sql'))
);

session_start();
error_reporting(E_ALL | E_STRICT);
date_default_timezone_set('UTC');

mb_language('uni');
mb_regex_encoding('UTF-8');
mb_internal_encoding('UTF-8');