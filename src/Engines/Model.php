<?php


namespace DevelMe\RestfulList\Engines;

use DevelMe\RestfulList\Contracts\Engine\Data;
use DevelMe\RestfulList\Contracts\Orchestration;
use Illuminate\Database\Eloquent\Builder;

final class Model extends Base implements Data
{
    protected Builder $data;

    public function __construct(Builder $data, Orchestration $orchestrator)
    {
        parent::__construct($orchestrator);

        $this->data = $data;
    }
}