<?php


namespace app\bframe\facades;


class Session
{
    public static function get($key)
    {
        return $_SESSION[$key];
    }

    public static function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public static function clear($key)
    {
        unset($_SESSION[$key]);
    }

    public static function check($key)
    {
        return isset($_SESSION[$key]);
    }
}