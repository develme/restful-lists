<?php


namespace DevelMe\RestfulList\Arrayable\Orchestration\Data;

use DevelMe\RestfulList\Contracts\Counter as CounterInterface;
use DevelMe\RestfulList\Contracts\Engine\Data;

class Counter implements CounterInterface
{
    public function count(Data $data): int
    {
        return count($data->data());
    }
}