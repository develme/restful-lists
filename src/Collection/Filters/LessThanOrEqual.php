<?php


namespace DevelMe\RestfulList\Collection\Filters;

class LessThanOrEqual extends SimpleCompare
{

    public function operator(): string
    {
        return "<=";
    }
}