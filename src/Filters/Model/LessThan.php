<?php


namespace DevelMe\RestfulList\Filters\Model;

class LessThan extends SimpleCompare
{
    public function operator(): string
    {
        return '<';
    }
}