<?php


namespace DevelMe\RestfulList\Engines;


use DevelMe\RestfulList\Contracts\Orchestration;

final class Collection extends Base
{
    private \Illuminate\Support\Collection $data;

    public function __construct(\Illuminate\Support\Collection $data, Orchestration $orchestrator)
    {
        parent::__construct($orchestrator);

        $this->data = $data;
    }
}