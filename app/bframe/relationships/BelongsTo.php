<?php


namespace app\bframe\relationships;


use app\bframe\facades\QB;

class BelongsTo extends Relationship
{

    public function get()
    {
        // localKey
        $localKeyName = $this->localeKey;

        if (is_null($this->result)) {
            $this->result = QB::query()
                ->select()
                ->where($this->foreignKey, '=', $this->model->$localKeyName)
                ->table($this->tableName)
                ->model($this->className)
                ->fetch();
        }

        return $this->result;
    }

    public function create($dataArr, $return = false)
    {
        throw new \Exception('Cannot create relationship like this');
    }


}