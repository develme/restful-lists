<?php


namespace DevelMe\RestfulList\Contracts\Comparator;

use Closure;

interface Defaults
{
    public function defaults(): Closure;
}