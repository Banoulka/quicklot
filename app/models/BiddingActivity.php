<?php


namespace app\models;


use app\bframe\abstracts\Model;

class BiddingActivity extends Model
{
    public int $amount;

    public string $created_at;

    protected static array $load = [
        'user'
    ];

    public function auction()
    {
        return $this->belongsTo(Auction::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}