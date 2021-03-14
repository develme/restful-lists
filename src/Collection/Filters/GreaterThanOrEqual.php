<?php


namespace DevelMe\RestfulList\Collection\Filters;


class GreaterThanOrEqual extends SimpleCompare
{

    public function operator(): string
    {
        return ">=";
    }
}