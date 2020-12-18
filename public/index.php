<?php

// Bootstrap for the project
use app\bframe\facades\Route;

require_once __DIR__ . "/../app/bframe/bootstrap.php";

// Routes for the project
require_once  __DIR__ . "/../routes.php";

// Some config items
require_once __DIR__ . "/../config/config.php";

// Resolve the routes in index
Route::resolve();