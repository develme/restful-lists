<?php


namespace DevelMe\RestfulList\Model\Orchestration\Count;

use DevelMe\RestfulList\Contracts\Counter as CounterInterface;
use DevelMe\RestfulList\Contracts\Engine\Data;

class Counter implements CounterInterface
{

    public function count(Data $data): int
    {
        return $data->data()->count();
    }
}