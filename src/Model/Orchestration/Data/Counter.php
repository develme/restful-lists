<?php


namespace DevelMe\RestfulList\Model\Orchestration\Data;

use DevelMe\RestfulList\Contracts\Counter as CounterInterface;
use DevelMe\RestfulList\Contracts\Engine\Data;

class Counter implements CounterInterface
{

    public function count(Data $data): int
    {
        return method_exists($data, 'prepared') && $data->prepared() ? $data->go()->count() : $data->data()->count();
    }
}