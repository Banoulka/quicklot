<?php


namespace app\bframe\facades;


class Validation
{
    private array $errors;

    public function __construct()
    {
        $this->errors = [];

    }

    public function required($key, $value)
    {
        if (!$value || empty($value)) {
            $this->pushError($key, 'is required');
        }
    }

    public function max($key, $value, $size)
    {
        if (is_string($value)) {
            if (strlen($value) > $size) {
                $this->pushError($key, "must be less than $size characters");
            }
        }
    }

    public function email($key, $value)
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->pushError($key, "must be a valid email address");
        }
    }

    public function unique($key, $value, $table)
    {
        $user = QB::query()->select()->table($table)->where($key, '=' , $value)->fetch();
        if ($user) {
            $this->pushError($key, "is already in use");
        }
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    private function pushError($key, $message)
    {
        $this->errors[$key][] = str_replace('_', ' ', ucwords($key),) . " " . $message;
    }
}