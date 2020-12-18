<?php


namespace app\bframe\middleware;


use app\bframe\facades\Request;
use app\bframe\facades\Route;

class Admin extends Middleware
{

    public static function resolve(Request $request)
    {
        if (!\app\bframe\auth\Auth::isAdmin()) {
            return;
        } else {
            Route::redirect($request->from);
        }
    }
}