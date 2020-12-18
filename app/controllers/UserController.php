<?php


namespace app\controllers;


use app\bframe\facades\Request;
use app\bframe\facades\Response;
use app\models\User;

class UserController
{
    public static function create(Request $request)
    {
        $request->validate([
            'email' => ['required', 'max:255', 'email', 'unique:users'],
            'name' => ['required', 'max:255'],
            'password' => ['required', 'max:255'],
            'confirm_password' => ['required', 'max:255']
        ], 'pages/signup');

        // TODO: Add confirmation
        $data = $request->only(['email', 'password', 'name']);

        // Hash the password
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);

        // Create the user
        User::create($data);

        // Send message back to client
        Response::render('/pages/signup_success');
    }
}