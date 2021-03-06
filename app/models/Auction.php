<?php


namespace app\models;


use app\bframe\abstracts\Model;
use app\bframe\facades\QB;
use app\models\lib\Database;
use app\models\lib\Helpers;
use app\models\lib\Image;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use DateTime;
use JsonSerializable;
use PDO;

class Auction extends Model implements JsonSerializable
{
    public string $start_date, $end_date;

    public int $price;

    private ?BiddingActivity $highest;

    public function lot()
    {
        return $this->belongsTo(Lot::class);
    }

    public function biddingActivity()
    {
        return $this->hasMany(BiddingActivity::class, null, 'bidding_activity', null, 'amount');
    }

    public function highestBid($refresh = false): ?BiddingActivity
    {
        if (!isset($this->highest) || $refresh) {
            $this->highest = QB::query()
                ->select()
                ->table('bidding_activity')
                ->where('auction_id', '=', $this->id)
                ->model(BiddingActivity::class)
                ->order('amount', QB::ORDER_DESC)
                ->limit(1)
                ->fetch() ?: null;
        }
        return $this->highest;
    }

    public static function all($sortBy = null, $category = null): array
    {
        $sql = "
            SELECT * FROM auctions 
            INNER JOIN lots l on auctions.lot_id = l.id";


        // Sort by latest, oldest or ending soon,
        // sets variables to build the sql further down
        if ($sortBy == 'lat') {
            $order = 'start_date';
            $desc = 'DESC';
        } else if ($sortBy == 'old') {
            $order = 'start_date';
            $desc = '';
        } else if ($sortBy == 'end') {
            $order = 'end_date';
            $desc = '';
        } else {
            $all = false;
            $order = '';
            $desc = '';
        }

        // Remove ended auctions
        if (!isset($all)) {
            $sql .= " WHERE end_date > NOW() ";
        }

        // Filter category
        if ($category) {
            if (isset($all))
                $sql .= " WHERE ";
            else
                $sql .= " AND ";

            $sql .= "l.category_id = $category->id";
        }

        // Sort By
        if (!empty($order)) {
            $sql .= " ORDER BY $order $desc";
        }

        // Prepare statements from DB
        $stmt = Database::db()->prepare($sql);

        $stmt->setFetchMode(PDO::FETCH_CLASS, static::class);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function endTime()
    {
        $start = new DateTime();
        $end = new DateTime($this->end_date);

        return "Ends in ".  Helpers::formatInterval(date_diff($start, $end));
    }

    public function images()
    {
        return $this->hasMany(Image::class, 'lot_id', 'lot_images', 'id');
    }

    public function category()
    {
        return $this->hasOne(Category::class, 'id', 'categories', 'category_id');
    }

    public function randomImage(): ?Image
    {
        /**
         * @var Image[] $images
         * @var Image $image
         */
        $images = $this->images()->get();

        if ($images)
            $image = $images[array_rand($images)];
        else
            return null;

        return $image;
    }

    public function isFinished(): bool
    {
        return Carbon::parse($this->end_date)->isPast();
    }

    public function delete()
    {
        // Delete bidding activity first
        QB::query()
            ->table('bidding_activity')
            ->remove([
                'auction_id' => $this->id
            ]);

        parent::delete(); // TODO: Change the autogenerated stub
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'end_date' => Carbon::parse($this->end_date)->diffForHumans([], CarbonInterface::DIFF_ABSOLUTE, false, 3)
        ];
    }
}