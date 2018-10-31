<?php

// Define constants
define('DOC_ROOT', realpath(dirname(__FILE__, 2)).DIRECTORY_SEPARATOR);
define('APP_ROOT', DOC_ROOT.'app'.DIRECTORY_SEPARATOR);
define('PUBLIC_ROOT', DOC_ROOT.'public'.DIRECTORY_SEPARATOR);
define('VIEW_ROOT', DOC_ROOT.'views'.DIRECTORY_SEPARATOR);

// Load Composer dependencies
require_once DOC_ROOT.'vendor/autoload.php';

// Initiate Environment variable helper
$dotenv = new Dotenv\Dotenv(DOC_ROOT);
$dotenv->load();

if (! function_exists('env')) {
    function env($key, $default = null) {
        $value = getenv($key);
        return ($value ? $value : $default);
    }
}

// Define core models
require_once APP_ROOT.'Route.php';
require_once APP_ROOT.'Request.php';
require_once APP_ROOT.'Controller.php';
require_once APP_ROOT.'Response.php';

// Load Application Helpers
$helpersRoot = APP_ROOT.'Helpers'.DIRECTORY_SEPARATOR;
foreach (array_diff(scandir($helpersRoot), array('..', '.')) as $filename) {
    if (is_file($helpersRoot.$filename)) {
        require_once $helpersRoot.$filename;
    }
}

// Define the application
require_once APP_ROOT.'App.php';


// Initiate the application
$GLOBALS['app'] = new App\App();

// Load helper functions
require_once APP_ROOT.'Helpers.php';

// Start the application
$GLOBALS['app']->handleRequest();