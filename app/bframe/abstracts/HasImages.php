<?php


namespace app\bframe\abstracts;


use app\bframe\relationships\HasMany;
use app\models\lib\Helpers;
use app\models\lib\Image;

trait HasImages
{
    // id field = x_id
    protected string $imageTable, $idField;

    public function saveImage($imageData)
    {
        $this->guessImageTable();
        $this->guessIdField();

        // Upload the file
        $dir = "images/uploads";
        $fileName = md5(microtime()) . "." .strtolower(pathinfo($imageData['name'], PATHINFO_EXTENSION));

        $targetFile = $dir . '/' . $fileName;

        move_uploaded_file($imageData['tmp_name'], $targetFile);

        // Create the database entry
        $this->images()->create([
            'path' => $fileName,
            $this->idField => $this->id
        ]);
    }

    public function images(): HasMany
    {
        $this->guessImageTable();
        return $this->hasMany(Image::class, null, $this->imageTable);
    }

    private function guessImageTable()
    {
        if (!isset($this->imageTable)) {
            $potentName = strtolower(Helpers::className($this)) . "_images";

            $this->imageTable = $potentName;
        }
    }

    private function guessIdField()
    {
        if (!isset($this->idField)) {
            $potentName = strtolower(Helpers::className($this)) . "_id";

            $this->idField = $potentName;
        }
    }
}