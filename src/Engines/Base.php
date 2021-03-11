<?php


namespace DevelMe\RestfulList\Engines;


use DevelMe\RestfulList\Contracts\Orchestration;

abstract class Base
{
    protected Orchestration $orchestrator;

    public function __construct(Orchestration $orchestrator)
    {
        $this->orchestrator = $orchestrator;
    }
}