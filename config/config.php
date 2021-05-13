<?php

// USER MODEL FOR AUTHENTICATION
use app\bframe\auth\Auth;
use app\models\User;
use Carbon\Carbon;
use Dotenv\Dotenv;

// Set the user model as the default model to authenticate against
Auth::setUserDriver(User::class);

// CARBON LOCALE
Carbon::setLocale('gb');
date_default_timezone_set('gb');

// LOAD DOT ENV
$dotenv = Dotenv::createImmutable(__DIR__ . "/../");

try {
    $dotenv->load();
} catch (Exception $exception) {
    echo 'ENVIRONMENT VARIABLES NOT FOUND';
    die();
}
