<?php


namespace DevelMe\RestfulList\Filters\Model;


class GreaterThan extends SimpleCompare
{

    public function operator(): string
    {
        return '>';
    }
}