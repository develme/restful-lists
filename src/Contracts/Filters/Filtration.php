<?php


namespace DevelMe\RestfulList\Contracts\Filters;

use DevelMe\RestfulList\Contracts\Engine\Data;

interface Filtration
{
    public function filter($data, Setting $setting);
}