<?php


namespace app\models;


use app\bframe\abstracts\Model;
use app\models\lib\Helpers;

class BiddingActivity extends Model implements \JsonSerializable
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

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'user' => $this->user,
            'amount' => $this->amount,
            'time' => Helpers::timeElapse($this->created_at),
        ];
    }
}