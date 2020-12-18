<?php


namespace app\bframe\abstracts;


use app\bframe\relationships\HasMany;

interface HasImageContract
{
    public function saveImage($imageData);

    public function images(): HasMany;
}