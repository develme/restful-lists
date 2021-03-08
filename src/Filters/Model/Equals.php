<?php


namespace DevelMe\RestfulList\Filters\Model;

use DevelMe\RestfulList\Contracts\Filters\Filtration;
use DevelMe\RestfulList\Contracts\Filters\Setting;

class Equals extends SimpleCompare
{
    public function operator(): string
    {
        return '=';
    }
}