<?php


namespace app\bframe\facades;


class Form
{
    private array $errorList;

    public static function NoForm()
    {
        return new Form([]);
    }

    public function __construct($errorList = [])
    {
        $this->errorList = $errorList;
    }

    public function setErrors($errorList)
    {
        $this->errorList = $errorList;
    }

    public function displayErrors($key): void
    {
        if ($this->hasErrors($key)) {
            foreach ($this->errorList[$key] as $msg) {
                echo "<p class=\"help is-danger\">$msg</p>";
            }
        }
    }

    public function allErrors(): array
    {
        return $this->errorList;
    }

    public function hasErrors($key): bool
    {
        return isset($this->errorList[$key]);
    }

    public function errorsClass($key): string
    {
        return $this->hasErrors($key) ? 'is-danger' : '';
    }

    public function isValid(): bool
    {
        return empty($this->errorList);
    }
}