<?php


namespace app\bframe\relationships;


use app\bframe\abstracts\Model;
use app\bframe\facades\QB;
use app\models\lib\Helpers;

class HasMany extends Relationship
{
    public function get()
    {
        $localKeyName = $this->localeKey;

        // DB QUERY TO GET
        if (empty($this->results)) {

            $qb = QB::query()
                ->select()
                ->where($this->foreignKey, '=', $this->model->$localKeyName)
                ->table($this->tableName)
                ->model($this->className);

            if (!is_null($this->orderBy))
                $qb->order($this->orderBy, QB::ORDER_DESC);

            $data = $qb->fetchAll();

            if ($data) {
                $this->results = $data;
            } else {
                $this->results = [];
            }
        }
        return $this->results;
    }
}