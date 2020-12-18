<?php


namespace app\bframe\auth;


trait Authenticatable
{
    protected static string $authAgainstProperty = 'email';
}