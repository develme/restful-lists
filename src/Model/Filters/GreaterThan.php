<?php


namespace DevelMe\RestfulList\Model\Filters;


class GreaterThan extends SimpleCompare
{

    public function operator(): string
    {
        return '>';
    }
}