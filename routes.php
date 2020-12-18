<?php

use app\bframe\facades\Route;
use app\controllers\AuctionController;
use app\controllers\AuthController;
use app\controllers\PageController;
use app\controllers\UserController;

// Main Page Routes
Route::get('/', [PageController::class, 'index']);

// Auth Routes
Route::get('/login', [PageController::class, 'login'])->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');

// User Routes
Route::get('/signup', [PageController::class, 'signup'])->middleware('guest');
Route::post('/signup', [UserController::class, 'create'])->middleware('guest');

// Profile
Route::get('/profile', [PageController::class, 'profile'])->middleware('auth');

// Auctions
Route::get('/auctions', [AuctionController::class, 'index']);
Route::both('/auctions/create', [AuctionController::class, 'create'])->middleware('auth');
Route::post('/auctions/new', [AuctionController::class, 'newAuction'])->middleware('auth');
Route::post('/auctions/remove', [AuctionController::class, 'remove'])->middleware('auth');
Route::get('/auctions/view', [AuctionController::class, 'view']);
Route::post('/auctions/bid', [AuctionController::class, 'bid'])->middleware('auth');