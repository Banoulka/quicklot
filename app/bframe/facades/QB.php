<?php


namespace app\bframe\facades;


use app\bframe\abstracts\Model;
use app\models\lib\Database;
use PDO;
use PDOStatement;

class QB
{
    private string $tableName;
    private string $orderBy;
    private string $desc;
    private ?string $model;
    private string $offset;
    private string $limit;
    private array $select;

    private array $results;
    private array $params = [];

    public const ORDER_DESC = 'DESC';

    public static function query()
    {
        return new QB();
    }

    public function remove($whereArr)
    {
        $sql = "DELETE FROM $this->tableName";

        foreach ($whereArr as $key => $value) {

            if ($key = array_key_first($whereArr)) {
                // First key
                $sql .= " WHERE ";
            } else {
                $sql .= " AND ";
            }

            $sql .= "$key = $value";
        }
        $stmt = Database::db()->prepare($sql);

        $stmt->execute();
    }

    public function select($cols = ['*'])
    {
        $this->select = $cols;
        return $this;
    }

    public function limit($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    public function table(string $tableName)
    {
        $this->tableName = $tableName;
        return $this;
    }

    public function where($colName, $operator, $value)
    {
        $this->params[$colName] = [
            'operator' => $operator,
            'value' => $value
        ];
        return $this;
    }

    public function order(string $orderBy, $desc = '')
    {
        $this->orderBy = $orderBy;
        $this->desc = $desc;
        return $this;
    }

    public function model($model)
    {
        $this->model = $model;
        return $this;
    }

    private function prepareFetch()
    {
        $sql = "SELECT ";

        // If there is no select option then select everything by default
        if (count($this->select) <= 1) {
            // Append one select, usually *
            $sql .= $this->select[0] . " ";
        } else {
            foreach ($this->select as $sel) {
                $sql .= "$sel, ";
            }
        }

        $sql .= "FROM $this->tableName";

        // WHERE CLAUSES
        // If there are 'where' clauses add them to the statement
        if (!empty($this->params)) {
            // there are params
            foreach ($this->params as $key => $value) {
                $keyName = $key . "Value";
                $operator = $value['operator'];

                // Paramter name binding with :value
                $sql .= " WHERE $key $operator :$keyName";
            }
        }

        // SORT BY
        if (isset($this->orderBy)) {
            $sql .= " ORDER BY $this->orderBy $this->desc";
        }

        // LIMIT ROWS
        if (isset($this->limit)) {
            $sql .= " LIMIT $this->limit";
        }

        $stmt = Database::db()->prepare($sql);

        // If there were any params, bind them now
        if (!empty($this->params)) {
            foreach ($this->params as $key => $value) {
                $val = $value['value'];
                $stmt->bindParam($key . 'Value', $val);
            }
        }

        // IF there is a definite model then fetch as the model
        if (isset($this->model) && class_exists($this->model)) {
            $stmt->setFetchMode(PDO::FETCH_CLASS, $this->model);
        }

        $stmt->execute();
        return $stmt;
    }

    public function fetch()
    {
        return $this->prepareFetch()->fetch();
    }

    public function fetchAll()
    {
        return $this->prepareFetch()->fetchAll();
    }

    public function insert($data, $return = false)
    {

        $sql = "INSERT INTO $this->tableName (";

        foreach ($data as $key => $value)
        {
            $sql .= $key;
            if ($key != array_key_last($data)) {
                // Not Last key
                $sql .= ', ';
            }
        }
        $sql .= ") VALUES ( ";

        foreach ($data as $key => $value) {
            $keyName = ":$key" . "Value";
            $sql .= $keyName;
            if ($key != array_key_last($data)) {
                // Not Last key
                $sql .= ', ';
            }
        }

        $sql .= ")";

        $stmt = Database::db()->prepare($sql);

        foreach ($data as $key => $value) {
            $keyName = ":$key" . "Value";
            $stmt->bindValue($keyName, $value);
        }

        $stmt->execute();

        if ($return) {
            $lastID = Database::db()->lastInsertId();

            // Perform the query
            return self::query()
                ->select()
                ->where('id', '=', $lastID)
                ->model($this->model)
                ->table($this->tableName)
                ->fetch();

        } else {
            return false;
        }
    }
}