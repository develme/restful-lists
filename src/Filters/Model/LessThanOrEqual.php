<?php


namespace DevelMe\RestfulList\Filters\Model;


class LessThanOrEqual extends SimpleCompare
{

    public function operator(): string
    {
        return '<=';
    }
}