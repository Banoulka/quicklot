<?php


namespace app\models;


use app\bframe\abstracts\Model;
use app\bframe\auth\User as Authenticatable;
use app\bframe\facades\QB;
use app\models\lib\Database;

// User -> user_id



class User extends Authenticatable implements \JsonSerializable
{
    public string $email, $password, $name;

    public ?string $remember_token, $profile_picture;

    // Define the relationship lots

    public function lots()
    {
        return $this->hasMany(Lot::class);
    }

    public function biddingList()
    {
        $sql = "
            SELECT l.*, a.*, ba.amount
            FROM bidding_activity ba
            RIGHT JOIN auctions a on ba.auction_id = a.id
            RIGHT JOIN lots l on a.lot_id = l.id
            WHERE ba.user_id = $this->id";

        $stmt = Database::db()->prepare($sql);

        $stmt->execute();

        $data = $stmt->fetchAll();

        return $data;
    }

    public function jsonSerialize()
    {
        return [
            'name' => $this->name,
            'profile_picture' => $this->profile_picture ? "uploads/users/" . $this->profile_picture : "placeholder.png",
        ];
    }

}