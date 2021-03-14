<?php


namespace DevelMe\RestfulList\Collection\Filters;


class Equals extends SimpleCompare
{
    public function operator(): string
    {
        return '=';
    }
}