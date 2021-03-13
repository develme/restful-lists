<?php


namespace DevelMe\RestfulList\Orders;


class Setting implements \DevelMe\RestfulList\Contracts\Orders\Setting
{
    private string $field;

    private string $direction;

    protected static string $defaultDirection = 'asc';

    public function __construct(string $field, string $direction)
    {
        $this->field = $field;
        $this->direction = $direction;
    }

    public static function createFromString(string $field, ?string $direction = null): static
    {
        return new static($field, $direction ?? static::$defaultDirection);
    }

    public static function createFromArray(array $setting, $name): static
    {
        return new static(
            field: $setting['field'] ?? $name,
            direction: $setting['direction'] ?? static::$defaultDirection,
        );
    }

    public function field(): string
    {
        return $this->field;
    }

    public function direction(): string
    {
        return $this->direction;
    }
}