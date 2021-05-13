<?php

namespace app\controllers\api;

use app\bframe\facades\Request;
use app\bframe\facades\Response;
use app\bframe\facades\Route;
use app\models\Auction;

class ApiAuctionController
{
    public static function bid(Request $request)
    {
        /**
         * @var Auction $auction
         */
        $auction = Auction::find($request->params['aid'] ?? -1);
        $data = [];

        if (!$auction) {
            $data['message'] = 'Auction not found';
            return Response::api($data)->status(404);
        }


        return Response::api($data);
    }
}