<?php


namespace DevelMe\RestfulList\Engines;


use DevelMe\RestfulList\Contracts\Orchestration;

final class Collection extends Base
{
    private \Illuminate\Support\Collection $data;
    /**
     * @var Orchestration
     */
    private Orchestration $orchestrator;

    public function __construct(\Illuminate\Support\Collection $data, Orchestration $orchestrator)
    {
        $this->data = $data;
        $this->orchestrator = $orchestrator;
    }
}