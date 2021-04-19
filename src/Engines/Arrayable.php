<?php

namespace DevelMe\RestfulList\Engines;

use DevelMe\RestfulList\Contracts\Orchestration;

class Arrayable extends Base
{
    protected array $data;

    public function __construct(array $data, Orchestration $orchestrator)
    {
        parent::__construct($orchestrator);

        $this->data = $data;
    }
}