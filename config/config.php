<?php

// USER MODEL FOR AUTHENTICATION
\app\bframe\auth\Auth::setUserDriver(\app\models\User::class);

// CARBON LOCALE
\Carbon\Carbon::setLocale('gb');
date_default_timezone_set('gb');

// LOAD DOT ENV
$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . "/../");
$dotenv->load();