<?php


namespace app\bframe\middleware;


use app\bframe\facades\Request;
use app\bframe\facades\Route;

class Auth
{
    public static function resolve(Request $request)
    {
        // If logged in then continue
        if (!\app\bframe\auth\Auth::loggedIn()) {
            Route::redirect('/login');
        } else {
            return;
        }

    }
}