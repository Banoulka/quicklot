<?php


namespace app\bframe\auth;


use app\bframe\abstracts\Model;

class User extends Model implements AuthContract
{
    use Authenticatable;

    protected static string $primaryKey = 'email';

    public static function getAuthAgainst(): string
    {
        return self::$authAgainstProperty;
    }
}