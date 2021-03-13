<?php


namespace DevelMe\RestfulList\Model\Filters;

class LessThan extends SimpleCompare
{
    public function operator(): string
    {
        return '<';
    }
}