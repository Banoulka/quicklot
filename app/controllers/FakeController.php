<?php


namespace app\controllers;


use app\bframe\facades\Request;
use app\bframe\facades\Route;

class FakeController
{
    public function fakeAuctions(Request $request)
    {



        Route::redirect('/auctions');
    }
}