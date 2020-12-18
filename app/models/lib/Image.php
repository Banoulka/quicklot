<?php


namespace app\models\lib;


use app\bframe\abstracts\Model;

class Image extends Model
{
    public string $path;

    public static function storeExternalUserImage($imageURL, $realStore = true)
    {
        $id = md5(microtime()) . ".jpg";
        $pathInfo = 'images/uploads/users/' . $id;

        if ($realStore)
            file_put_contents($pathInfo, file_get_contents($imageURL));

        return $id;
    }
}