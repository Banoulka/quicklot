<?php

namespace app\controllers;

use app\bframe\abstracts\Controller;
use app\bframe\auth\Auth;
use app\bframe\facades\Form;
use app\bframe\facades\QB;
use app\bframe\facades\Request;
use app\bframe\facades\Response;
use app\bframe\facades\Route;
use app\models\Auction;
use app\models\BiddingActivity;
use app\models\lib\Database;
use app\models\lib\Image;
use app\models\Lot;
use app\models\User;
use Carbon\Carbon;

class PageController extends Controller
{
    public static function index(Request $request)
    {
//        self::generateRandomUsers(500);
//        self::generateRandomLots('women\'s clothing', 6);

        Response::render('pages/index');
    }

    public static function login(Request $request)
    {
        Response::render('pages/login');
    }

    public static function signup(Request $request)
    {
        Response::render('pages/signup');
    }

    public static function profile(Request $request)
    {
        Response::render('pages/profile');
    }

    public static function generateRandomUsers($users = 1)
    {
        $content = false;
        try {
            $content = file_get_contents(
                "https://randomuser.me/api/?results=$users&nat=gb,us,au&exc=id,gender,location,registered,phone,cell,dob&noinfo"
            );
        } catch(\Exception $exception) {
            echo ' failed to get content <br/>';
        }

        if ($content) {
            $json = json_decode($content);
            $results = $json->results;

            foreach ($results as $userData) {
                // IMAGE UPLOAD
                $image = $userData->picture->large;
                $id = Image::storeExternalUserImage($image, true);

                // USER CREATION
                $realData = [
                    'name' => $userData->name->first . " " . $userData->name->last,
                    'password' => '$2y$10$4itqN/IuqOZMJ9RwdzhQbupWLbqXFzTig/jRv3wzQnodJN1EuJY8a',
                    'remember_token' => md5(microtime()),
                    'email' => $userData->email,
                    'display_name' => $userData->login->username,
                    'profile_picture' => $id
                ];

                User::create($realData);
                echo "created user " . $realData['email'] . '<br/>';
            }
        }
    }

    public static function generateRandomLots($category, $cat_id)
    {
        /**
         * @var User $user
         * @var Lot $lot
         * @var Auction $auction
         * @var User $auctioner
         */
        $content = file_get_contents("https://fakestoreapi.com/products/category/$category");
        $json = json_decode($content);

        Database::db()->beginTransaction();

        $now = Carbon::now();

        foreach ($json as $product) {
            $user = User::random();
            $future = $now->addDays(rand(0, 6))->addHours(rand(0, 20))->addMinutes(rand(0, 50));

            $lot = $user->lots()->create([
                'name' => $product->title,
                'description' => $product->description,
                'category_id' => $cat_id
            ], true);

            // Images for LOT
            $image = $product->image;
            $id = md5(microtime()) . ".jpg";

            $pathInfo = 'public/images/uploads/' . $id;

            file_put_contents($pathInfo, file_get_contents($image));

            // Update DB
            QB::query()
                ->table('lot_images')
                ->insert([
                    'lot_id' => $lot->id,
                    'path' => $id
                ]);

            $auction = $lot->auction()->create([
                'price' => $product->price,
                'end_date' => $future->toDateTimeString()
            ], true);

            // Random bidding activity for the auction
            $auctioners = rand(0, 12) + rand(0, 10);

            for ($i = 0; $i < $auctioners; $i++) {
                $auctioner = User::random();

                $highest = $auction->highestBid(true)->amount ?? $auction->price;

                $randomAmount = rand($highest, $highest+30);

                if ($auctioner->id !== $user->id) {
                    $biddingActivity = $auction->biddingActivity()->create([
                        'user_id' => $auctioner->id,
                        'amount' => $randomAmount
                    ], true);

//                    var_dump($biddingActivity);
                }
            }
        }
//        die();

        Database::db()->commit();
//        Database::db()->rollBack();
    }
}