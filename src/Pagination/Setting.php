<?php


namespace DevelMe\RestfulList\Pagination;


class Setting implements \DevelMe\RestfulList\Contracts\Pagination\Setting
{
    private int $start;

    private int $end;

    public function __construct(int $start, int $end)
    {
        $this->start = $start;
        $this->end = $end;
    }

    public static function createFromAssociative(array $setting)
    {
        return new static((int)$setting['start'], (int)$setting['end']);
    }

    public static function createFromOrdered(array $setting)
    {
        return new static((int)$setting[0], (int)$setting[1]);
    }

    public function start(): int
    {
        return $this->start;
    }

    public function end(): int
    {
        return $this->end;
    }
}