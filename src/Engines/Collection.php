<?php


namespace DevelMe\RestfulList\Engines;

use DevelMe\RestfulList\Contracts\Orchestration;
use Illuminate\Support\Collection as IlluminateCollection;

final class Collection extends Base
{
    protected IlluminateCollection $data;

    public function __construct(IlluminateCollection $data, Orchestration $orchestrator)
    {
        parent::__construct($orchestrator);

        $this->data = $data;
    }
}