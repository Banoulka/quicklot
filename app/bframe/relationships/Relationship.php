<?php


namespace app\bframe\relationships;


use app\bframe\abstracts\Model;
use app\bframe\facades\QB;
use app\models\lib\Helpers;

abstract class Relationship
{
    protected array $results = [];
    protected ?object $result = null;

    protected string $foreignKey, $tableName, $localeKey;
    protected string $className;

    protected ?string $orderBy;

    protected Model $model;

    public function __construct($className, $foreignKey, $tableName, Model $model, $localeKey = null, $orderBy = null)
    {
        $this->className = $className;
        $this->model = $model;
        $this->foreignKey = $foreignKey;
        $this->tableName = $tableName;
        $this->orderBy = $orderBy;

        if (isset($localeKey)) {
            $this->localeKey = $localeKey;
        }
    }

    public function create($dataArr, $return = false)
    {
        $trueData = array_merge($dataArr, [
            $this->foreignKey => $this->model->id
        ]);

        return QB::query()
            ->table($this->tableName)
            ->model($this->className)
            ->insert($trueData, $return);
    }

    public abstract function get();

//    public function with($dataArr)
//    {
//        // Original get method
//        $result = $this->get();
//
//        if (is_array($result)) {
//
//            foreach ($result as $obj) {
//                var_dump($obj);
//                die();
//            }
//
//        } else {
//
//        }
//
//        var_dump($this, $dataArr, $result);
//        die();
//
//
//        return $result;
//    }
}