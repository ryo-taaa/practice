<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = '/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require '/Applications/MAMP/htdocs/practice/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
(require_once '/Applications/MAMP/htdocs/practice/bootstrap/app.php')
    ->handleRequest(Request::capture());
