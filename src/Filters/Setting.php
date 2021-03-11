<?php


namespace DevelMe\RestfulList\Filters;


use DevelMe\RestfulList\Contracts\Filters\Setting as FilterSettingInterface;
use DevelMe\RestfulList\Contracts\Filters\Setting as SettingContract;
use DevelMe\RestfulList\Filters\Setting as FilterSetting;

class Setting implements SettingContract
{
    protected string $field;

    protected string $type;

    protected mixed $value;

    protected static string $defaultType = 'equals';

    public function __construct(string $field, string $type, mixed $value)
    {
        $this->field = $field;
        $this->type = $type;
        $this->value = $value;
    }

    public static function createFromString(string $setting, string $name): static
    {
        return new static($name, static::$defaultType, $setting);
    }

    public static function createFromArray(array $setting, $name): static
    {
        return new static(
            field: $setting['field'] ?? $name,
            type: $setting['type'] ?? 'equals',
            value: $setting['value'] ?? $setting
        );
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