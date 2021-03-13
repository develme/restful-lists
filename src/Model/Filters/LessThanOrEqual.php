<?php


namespace DevelMe\RestfulList\Model\Filters;


class LessThanOrEqual extends SimpleCompare
{

    public function operator(): string
    {
        return '<=';
    }
}