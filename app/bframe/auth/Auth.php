<?php


namespace app\bframe\auth;

use app\bframe\facades\Session;

class Auth
{
    protected static string $userDriver;

    public static function attempt($credentials): bool
    {
        if (!class_implements(self::$userDriver, AuthContract::class)) {
            throw new \Exception('hello');
        }

        // Find the model
        /**
         * @var AuthContract $authenticatable
         */
        $authenticatable =
            call_user_func(
                [self::$userDriver, 'find'], $credentials[(self::$userDriver)::getAuthAgainst()]
            );

        // If no 'user' was found then return false
        if (!$authenticatable)
            return false;

        // Verify the password against the found user
        if (password_verify($credentials['password'] ,$authenticatable->password)) {
            Session::set('auth', serialize($authenticatable));
            return true;
        }

        return false;
    }

    public static function logout()
    {
        // Unset the session
        Session::clear('auth');
    }

    public static function loggedIn()
    {
        return Session::check('auth');
    }

    public static function isAdmin()
    {
        return false;
    }

    public static function User()
    {
        return Session::check('auth') ? unserialize(Session::get('auth')) : null;
    }

    public static function setUserDriver($model)
    {
        Auth::$userDriver = $model;
    }
}