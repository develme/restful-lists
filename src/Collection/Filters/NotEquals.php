<?php

namespace DevelMe\RestfulList\Collection\Filters;

class NotEquals extends SimpleCompare
{

    public function operator(): string
    {
        return '!=';
    }
}