<?php


namespace DevelMe\RestfulList\Filters\Model;

class GreaterThanOrEqual extends SimpleCompare
{

    public function operator(): string
    {
        return '>=';
    }
}