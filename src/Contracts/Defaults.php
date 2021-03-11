<?php


namespace DevelMe\RestfulList\Contracts;

use Closure;

interface Defaults
{
    public function defaults(): Closure;
}