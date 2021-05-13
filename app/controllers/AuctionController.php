<?php


namespace app\controllers;


use app\bframe\auth\Auth;
use app\bframe\facades\Request;
use app\bframe\facades\Response;
use app\bframe\facades\Route;
use app\models\Auction;
use app\models\Category;
use app\models\lib\Database;
use app\models\Lot;
use app\models\User;
use Carbon\Carbon;

class AuctionController
{
    protected static array $validSorts = [
        'all',
        'lat',
        'old',
        'end'
    ];


    public static function index(Request $request)
    {
        // Category
        $categories = Category::all();
        $currCat = Category::find($request->params['cat'] ?? -1);

        // Sorting
        $sortBy = $request->params['od'] ?? 'all';

        // Default value if not in array
        if (!in_array($sortBy, self::$validSorts)) {
            $sortBy = 'all';
        }

        // Auctions, use sort by and category
        $allAuctions = Auction::all($sortBy, $currCat);
//        $allAuctions = [];

        Response::render('auctions/index', [
            'categories' => $categories,
            'currentCategory' => $currCat,
            'auctions' => $allAuctions,
            'sortBy' => $sortBy
        ]);
    }

    public static function newAuction(Request $request)
    {
        // Create a new auction
        $auctionData = $request->only(['price', 'end_date']);

        // Find the LOT associated with the id
        $lot = Lot::find($request->post->lot_id);

        if ($lot) {
            $lot->auction()->create($auctionData);
        }

        Route::redirect('/profile');
    }

    public static function create(Request $request)
    {
        $categories = Category::all();

        if ($request->isGet()) {
            Response::render('auctions/create', [
                'categories' => $categories
            ]);
            exit;
        }

        $request->validate([
            'name' => 'required|max:255',
            'description' => 'required|max:1000',
            'category' => 'required',
        ], 'auctions/create');


        $lotData = $request->only(['name', 'description']);

        $lotData['category_id'] = $request->all()['category'];

        /**
         * @var User $currentUser
         */
        $currentUser = Auth::User();

        Database::db()->beginTransaction();

        try {

            /**
             * Add a lot to the current user
             * @var Lot $lot
             */
            $lot = $currentUser->lots()->create($lotData, true);

            // Add the images to the lot
            $images = $request->files['images'];
            $imageCount = count($request->files['images']['name']);

            for ($i = 0; $i < $imageCount; $i++) {
                $currFile = [
                    'name' => $images['name'][$i],
                    'type' => $images['type'][$i],
                    'tmp_name' => $images['tmp_name'][$i],
                    'error' => $images['error'][$i],
                    'size' => $images['size'][$i],
                ];
                $lot->saveImage($currFile);
            }

            // IF the auction is set to immediate
            $auctionData = $request->only(['price', 'end_date']);

            if (!empty($auctionData['price']) && !empty($auctionData['end_date'])) {

                // Start a new auction
               $auction = $lot->auction()->create($auctionData, true);
            }

        } catch (\Exception $e) {
            Database::db()->rollBack();
            throw new \Exception($e);
        }

        Database::db()->commit();

        // Redirect to the profile page after insert completed
//        Database::db()->rollBack();

        Route::redirect('/profile');
    }

    public static function remove(Request $request)
    {
        /**
         * @var Auction $auction
         * @var Lot $lot
         */
        $auction = Auction::find($request->post->auction_id);

        // Auction exists
        if ($auction) {

            // Check lot user against auth user
            $lot = $auction->lot()->get();

            $userAuthorised = $lot->owner()->get()->id === Auth::User()->id;

            if ($userAuthorised) {
                // Remove the auction
                $auction->delete();
            }
        }

        Route::redirect('/profile');
    }

    public static function view(Request $request)
    {
        $lot = Lot::find($request->params['aid'] ?? -1);
        $auction = $lot ? $lot->auction()->get() : false;

        if (!$auction) {
            Response::render('_404')->status(404);
        }

        $auction->lot = $lot;

        // Auction is valid show auction page
        Response::render('auctions/show', [
            'auction' => $auction
        ]);
    }

    public static function bid(Request $request)
    {
        /**
         * @var Auction $auction
         */
        $auction = Auction::find($request->post->auction_id ?? -1);

        if (!$auction) {
            Route::redirect('/auctions');
        }

        $auction->lot = $auction->lot()->get();

        $bid = (int) $request->post->amount;
        $highest = $auction->highestBid()->amount ?? $auction->price;

        if ($bid <= $highest) {
            $errors = [
                'bid' => $bid,
                'error' => 'Bid amount must be higher than the current price'
            ];
        }

        // Normal data to send back
        $sendBack = ['auction' => $auction];

        if (isset($errors)) {
            Response::render('auctions/show', array_merge($sendBack, $errors));
            return;
        }

        // If no errors, add to database then send back
        $auction->biddingActivity()->create([
            'user_id' => Auth::User()->id,
            'amount' => $bid,
        ]);

        $id = $auction->lot->id;
        Route::redirect("/auctions/view?aid=$id");
    }
}