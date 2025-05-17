<?php

/**
 * A sample model used in demo examples for ModelFaker.
 */
class DemoModel
{
    public string $name;
    public int $age;
    public string $gender;

    public function setInteger(int $value): void
    {
        var_dump($value);
    }

    public function setString(string $value): void
    {
        var_dump($value);
    }

    public function setBoolean(bool $value): void
    {
        var_dump($value);
    }

    public function setFloat(float $value): void
    {
        var_dump($value);
    }

    public function setDescription(string $value): void
    {
        var_dump($value);
    }

    public function setDatetime(DateTime $value): void
    {
        var_dump($value);
    }
}
