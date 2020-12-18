<?php


namespace app\bframe\abstracts;


use app\bframe\facades\QB;
use app\models\lib\Database;
use app\models\lib\Helpers;
use JsonSerializable;
use PDO;

abstract class Model implements JsonSerializable
{
    use HasRelationships;

    public string $id;

    protected static string $dbName = '';

    // PRIMARY KEY FOR MODEL
    protected static string $primaryKey = 'id';

    // Eager loaded attributes
    protected static array $load = [];

    public function __construct()
    {
        if (!isset($this->dbName) || empty($this->dbName)) {
            $this->guessTableName();
        }

        $this->loadEagerModels();

        $this->afterLoad();
    }

    private function loadEagerModels()
    {
        foreach (static::$load as $model) {
            // Check for a method name in this class
            if (method_exists($this, $model)){
                $this->$model = $this->$model()->get();
            }
        }
    }

    public function getPrimaryKey()
    {
        return static::$primaryKey;
    }

    protected function afterLoad(){}

    public function getDbName()
    {
        return static::$dbName;
    }

    private static function guessTableName()
    {
        if (static::$dbName == '') {
            $modelLower = strtolower(Helpers::staticClass(static::class));

            if (substr($modelLower, -1) != 's') {
                // Model is not plural, add s on
                $modelLower .= "s";
            }

            static::$dbName = $modelLower;
        }
    }

    public static function all()
    {
        static::guessTableName();
        return QB::query()
            ->model(static::class)
            ->select()
            ->table(static::$dbName)
            ->fetchAll();
    }

    public static function find($value)
    {
        static::guessTableName();
        return QB::query()
            ->model(static::class)
            ->select()
            ->where(static::$primaryKey, '=', $value)
            ->table(static::$dbName)
            ->fetch();
    }

    public static function create($dataArr)
    {
        static::guessTableName();
        QB::query()
            ->table(static::$dbName)
            ->insert($dataArr);
    }

    public static function random()
    {
        static::guessTableName();
        $table = static::$dbName;
        $class = static::class;

        $sql = "SELECT * FROM $table
                ORDER BY RAND()
                LIMIT 1";

        $stmt = Database::db()->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_CLASS, $class);

        $stmt->execute();

        return $stmt->fetch();
    }

    public function delete()
    {
        QB::query()
            ->table(static::$dbName)
            ->remove([
                'id' => $this->id
            ]);
    }

    public function jsonSerialize()
    {
        // TODO: Implement jsonSerialize() method.
    }
}