<?php


namespace DevelMe\RestfulList\Arrayable\Orchestration;


use DevelMe\RestfulList\Engines\Orchestration\Orchestrator as EngineOrchestrator;

class Orchestrator extends EngineOrchestrator
{
    protected static string $defaults = Composition::class;
}