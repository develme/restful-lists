<?php


namespace DevelMe\RestfulList\Collection\Filters;

class GreaterThan extends SimpleCompare
{

    public function operator(): string
    {
        return ">";
    }
}