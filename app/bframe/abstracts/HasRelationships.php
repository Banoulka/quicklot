<?php


namespace app\bframe\abstracts;


use app\bframe\relationships\BelongsTo;
use app\bframe\relationships\HasMany;
use app\bframe\relationships\HasOne;
use app\models\lib\Helpers;

trait HasRelationships
{
    protected function hasMany($class, $foreignKey = null, $otherTable = null, $localeKey = null, $orderBy = null)
    {
        $foreignKey = $foreignKey ?: $this->guessForeignKey();

        $otherTable = $otherTable ?: $this->guessOtherTableName($class);

        $localeKey = $localeKey ?: 'id';

        return new HasMany($class, $foreignKey, $otherTable, $this, $localeKey, $orderBy);
    }

    protected function belongsTo($class, $foreignKey = null, $otherTable = null, $localeKey = null)
    {
        $foreignKey = $foreignKey ?: 'id';

        $localeKey = $localeKey ?: $this->guessLocalKey($class);

        $otherTable = $otherTable ?: $this->guessOtherTableName($class);

        return new BelongsTo($class, $foreignKey, $otherTable, $this, $localeKey);
    }

    protected function hasOne($class, $foreignKey = null, $otherTable = null, $localeKey = null)
    {
        $foreignKey = $foreignKey ?: $this->guessForeignKey();

        $otherTable = $otherTable ?: $this->guessOtherTableName($class);

        return new HasOne($class, $foreignKey, $otherTable, $this, $localeKey);
    }

    private function guessLocalKey($class): string
    {
        return strtolower(Helpers::staticClass($class)) . "_id";
    }

    private function guessForeignKey(): string
    {
        // Guess the foreign key of the model based on this class
        return strtolower(Helpers::className($this)) . "_id";
    }

    private function guessOtherTableName($class): string
    {
        // Guess the locale key of the model
        return strtolower(Helpers::staticClass($class)) . "s";
    }
}