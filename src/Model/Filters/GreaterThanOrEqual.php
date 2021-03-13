<?php


namespace DevelMe\RestfulList\Model\Filters;

class GreaterThanOrEqual extends SimpleCompare
{

    public function operator(): string
    {
        return '>=';
    }
}