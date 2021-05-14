<?php


namespace app\bframe\middleware;


use app\bframe\facades\Request;
use app\bframe\facades\Route;
use app\bframe\auth\Auth;

class Guest extends Middleware
{
    public static function resolve(Request $request)
    {
        if ( Auth::loggedIn() ) {
            Route::redirect($request->from ?? '/');
        } else {
            // do nothing
            return;
        }
    }
}