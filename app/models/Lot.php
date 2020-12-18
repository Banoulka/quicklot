<?php


namespace app\models;


use app\bframe\abstracts\HasImageContract;
use app\bframe\abstracts\HasImages;
use app\bframe\abstracts\Model;
use app\bframe\relationships\HasMany;
use app\models\lib\Image;

class Lot extends Model implements HasImageContract
{
    use HasImages;

    public string $name, $description;

    protected static array $load = [
        'category',
        'images',
        'auction'
    ];

    public function owner()
    {
        return $this->belongsTo(User::class);
    }

    public function auction()
    {
        return $this->hasOne(Auction::class);
    }

    public function category()
    {
        return $this->hasOne(Category::class, 'id', 'categories', 'category_id');
    }
}