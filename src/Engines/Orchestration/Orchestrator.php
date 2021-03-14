<?php


namespace DevelMe\RestfulList\Engines\Orchestration;

use DevelMe\RestfulList\Concerns\Composition\WithDefaults;
use DevelMe\RestfulList\Contracts\Counter;
use DevelMe\RestfulList\Contracts\Engine\Arrangement;
use DevelMe\RestfulList\Contracts\Engine\Filtration;
use DevelMe\RestfulList\Contracts\Engine\Paginator;
use DevelMe\RestfulList\Contracts\Engine\Result;
use DevelMe\RestfulList\Contracts\Orchestration;
use DevelMe\RestfulList\Contracts\Registration;


/**
 * @method Counter counter()
 * @method Filtration filter()
 * @method Arrangement order()
 * @method Paginator pagination()
 * @method Result result()
 */
abstract class Orchestrator implements Registration, Orchestration
{
    use WithDefaults;

    public function orchestrate(string $ask): mixed
    {
        $compositions = $this->compositions;

        return $compositions($ask);
    }

    public function __call(string $name, array $arguments)
    {
        return $this->orchestrate($name);
    }
}