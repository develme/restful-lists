<?php


namespace DevelMe\RestfulList\Contracts;

use DevelMe\RestfulList\Contracts\Engine\Data;

interface Counter
{
    public function count(Data $data): int;
}