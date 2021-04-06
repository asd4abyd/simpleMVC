<?php


//var_dump($_REQUEST);
//var_dump($_SERVER['REQUEST_URI']);

define('APP_ENV', 'test');


define('BASE_PATH', __DIR__);
define('APP_PATH', BASE_PATH.'/app/');
define('CORE_PATH', BASE_PATH.'/core/');
define('RESOURCES_PATH', BASE_PATH.'/resources/');
define('HELPERS_PATH', BASE_PATH.'/helpers/');


if(APP_ENV=='production'){
    error_reporting(0);
    ini_set('display_errors', '0');
}
else {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
}

require_once CORE_PATH.'Init.php';