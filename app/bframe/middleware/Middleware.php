<?php


namespace app\bframe\middleware;


use app\bframe\facades\Request;

abstract class Middleware
{
    public abstract static function resolve(Request $request);
}