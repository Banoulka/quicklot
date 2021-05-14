<?php

namespace app\controllers\api;

use app\bframe\facades\Request;
use app\bframe\facades\Response;
use app\models\Auction;

class ApiBiddingActivityController
{
    public static function bids(Request $request) {
        /**
         * @var Auction $auction
         */
        $auction = Auction::find($request->params['aid'] ?? -1);

        if (!$auction) {
            $data['message'] = 'Auction not found';
            return Response::api($data)->status(404);
        }

        $data['bids'] = $auction->biddingActivity()->get();
        $data['highest'] = $auction->highestBid();
        $data['auction'] = $auction;

        return Response::api($data);
    }
}