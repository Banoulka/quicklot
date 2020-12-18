<?php


namespace app\controllers;


use app\bframe\auth\Auth;
use app\bframe\facades\Form;
use app\bframe\facades\Request;
use app\bframe\facades\Response;
use app\bframe\facades\Route;

class AuthController
{
    public static function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'max:255', 'email'],
            'password' => ['required', 'max:255']
        ], 'pages/login');

        $data = $request->all();

        // Validations are correct
        if (Auth::attempt($data)) {
            Route::redirect('/auctions');
        } else {
            Response::render('/pages/login', [
                'error' => 'These credentials do not match our records',
                'data' => $data,
            ]);
        }
    }

    public static function logout(Request $request)
    {
        Auth::logout();

        Route::redirect($request->from ?? '/login');
    }
}