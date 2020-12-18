<?php


namespace app\controllers;


use app\bframe\facades\Request;
use app\bframe\facades\Response;

class LotController
{
    public static function index(Request $request)
    {
        // List all lots belonging to that user
        $lots = [];
        Response::render("pages/lots/index", [
            'lots' => $lots,
        ]);
    }
}