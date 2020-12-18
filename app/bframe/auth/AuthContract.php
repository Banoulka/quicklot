<?php


namespace app\bframe\auth;


interface AuthContract
{
    public static function getAuthAgainst(): string;
}