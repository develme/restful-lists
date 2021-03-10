<?php


namespace DevelMe\RestfulList\Orders;


class Setting implements \DevelMe\RestfulList\Contracts\Orders\Setting
{
    private string $field;

    private string $direction;

    public function __construct(string $field, string $direction)
    {
        $this->field = $field;
        $this->direction = $direction;
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