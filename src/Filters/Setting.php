<?php


namespace DevelMe\RestfulList\Filters;


use DevelMe\RestfulList\Contracts\Filters\Setting as SettingContract;

class Setting implements SettingContract
{
    protected string $field;

    protected string $type;

    protected mixed $value;

    public function __construct(string $field, string $type, mixed $value)
    {
        $this->field = $field;
        $this->type = $type;
        $this->value = $value;
    }

    public function field(): string
    {
        return $this->field;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function value(): mixed
    {
        return $this->value;
    }
}